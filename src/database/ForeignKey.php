<?php
namespace yentu\database;

use yentu\exceptions\SyntaxErrorException;

class ForeignKey extends DatabaseItem implements Commitable, Changeable, Initializable
{
    private Table $table;
    private array $columns;
    private Table $foreignTable;
    private array $foreignColumns;
    private string $name = '';
    public string $onDelete;
    public string $onUpdate;
    public static string $defaultOnDelete = 'NO ACTION';
    public static string $defaultOnUpdate = 'NO ACTION';

    public function __construct(array $columns, Table $table)
    {
        $this->table = $table;
        $this->columns = $columns;
        $this->onDelete = self::$defaultOnDelete;
        $this->onUpdate = self::$defaultOnUpdate;
    }

    #[\Override]
    public function initialize()
    {
        $name = $this->getChangeLogger()->doesForeignKeyExist([
            'schema' => $this->table->getSchema()->getName(),
            'table' => $this->table->getName(),
            'columns' => $this->columns
        ]);
        if ($name === false) {
            $this->new = true;
        } else {
            $this->name = $name;
        }
    }

    /**
     * 
     * @param \yentu\database\Table $table
     * @return \yentu\database\ForeignKey
     */
    public function references(Table $table): DatabaseItem
    {
        if ($table->isReference()) {
            $this->foreignTable = $table;
        } else {
            throw new \yentu\exceptions\DatabaseManipulatorException(
                    "References cannot be created from a non referencing table. "
                    . "Please use either a reftable() or refschema() "
                    . "construct to link a referenced table"
            );
        }
        return $this;
    }

    public function columns(string ...$args)
    {
        $this->foreignColumns = $args;
        return $this;
    }

    public function drop()
    {
        $description = $this->getChangeLogger()->getDescription();
        $key = $description['schemata'][$this->table->getSchema()->getName()]['tables'][$this->table->getName()]['foreign_keys'][$this->name];

        $this->getChangeLogger()->dropForeignKey(
            array(
                'columns' => $this->columns,
                'table' => $this->table->getName(),
                'schema' => $this->table->getSchema()->getName(),
                'foreign_columns' => $key['foreign_columns'],
                'foreign_table' => $key['foreign_table'],
                'foreign_schema' => $key['foreign_schema'],
                'name' => $this->name,
                'on_delete' => $key['on_delete'],
                'on_update' => $key['on_update']
            )
        );
        return $this;
    }

    private function validate()
    {
        if (!is_array($this->foreignColumns)) {
            throw new SyntaxErrorException("No foreign columns specified for foreign key {$this->name}", $this->home);
        }
    }

    #[\Override]
    public function commitNew()
    {
        $this->validate();
        if ($this->name == '' && is_object($this->foreignTable)) {
            $this->name = $this->table->getName() . '_' . implode('_', $this->columns) .
                '_' . $this->foreignTable->getName() .
                '_' . implode('_', $this->foreignColumns) . '_fk';
        } else if (!is_object($this->foreignTable)) {
            throw new \yentu\exceptions\DatabaseManipulatorException(
                    "No references defined for foreign key {$this->name}"
            );
        }

        $this->getChangeLogger()->addForeignKey($this->buildDescription());
    }

    public function name($name)
    {
        if ($this->getChangeLogger()->doesForeignKeyExist([
                'schema' => $this->table->getSchema()->getName(),
                'table' => $this->table->getName(),
                'name' => $name
            ])) {
            $this->setKeyDetails($name);
            $this->new = false;
        }
        $this->name = $name;
        return $this;
    }

    private function setKeyDetails($name)
    {
        $foreignKey = $this->getChangeLogger()
                ->getDescription()
                ->getTable([
                    'table' => $this->table->getName(),
                    'schema' => $this->table->getSchema()->getName()
                    ]
                )['foreign_keys'][$name];
        $this->columns = $foreignKey['columns'];
        $this->foreignTable = new Table($foreignKey['foreign_table'], new Schema($foreignKey['foreign_schema']));
        $this->foreignTable->setIsReference(true);
        $this->foreignColumns = $foreignKey['foreign_columns'];
        $this->onUpdate = $foreignKey['on_update'];
        $this->onDelete = $foreignKey['on_delete'];
    }

    public function onDelete($onDelete)
    {
        return $this->addChange('onDelete', 'on_delete', $onDelete);
    }

    public function onUpdate($onUpdate)
    {
        return $this->addChange('onUpdate', 'on_update', $onUpdate);
    }

    #[\Override]
    public function buildDescription()
    {
        if ($this->foreignTable === null) {
            throw new \yentu\exceptions\DatabaseManipulatorException(
                    "No references defined for foreign key {$this->name}"
            );
        }
        return array(
            'columns' => $this->columns,
            'table' => $this->table->getName(),
            'schema' => $this->table->getSchema()->getName(),
            'foreign_columns' => $this->foreignColumns,
            'foreign_table' => $this->foreignTable->getName(),
            'foreign_schema' => $this->foreignTable->getSchema()->getName(),
            'name' => $this->name,
            'on_delete' => $this->onDelete,
            'on_update' => $this->onUpdate
        );
    }
}
