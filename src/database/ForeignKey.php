<?php

namespace yentu\database;

class ForeignKey extends DatabaseItem
{
    /**
     *
     * @var Table
     */
    private $table;
    private $columns;
    private $foreignTable;
    private $foreignColumns;
    private $name;
    private $nameSet;
    
    public $onDelete;
    public $onUpdate;
    public static $defaultOnDelete = 'NO ACTION';
    public static $defaultOnUpdate = 'NO ACTION';

    public function __construct($columns, $table)
    {
        $this->table = $table;
        $this->columns = $columns;
        $this->onDelete = self::$defaultOnDelete;
        $this->onUpdate = self::$defaultOnUpdate;

        // Prevent the committing of the foreign key even if the context
        // switches

        $name = $this->getDriver()->doesForeignKeyExist([
            'schema' => $table->getSchema()->getName(),
            'table' => $table->getName(),
            'columns' => $columns
        ]);
        if($name === false) {
            $this->new = true;
        } else {
            $this->name = $name;
            $this->nameSet = true;
        }
    }

    /**
     * 
     * @param \yentu\database\Table $table
     * @return \yentu\database\ForeignKey
     */
    public function references($table)
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

    public function columns()
    {
        $this->foreignColumns = func_get_args();
        return $this;
    }

    public function drop()
    {
        $description = $this->getDriver()->getDescription();
        $key = $description['schemata'][$this->table->getSchema()->getName()]['tables'][$this->table->getName()]['foreign_keys'][$this->name];

        $this->getDriver()->dropForeignKey(
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
        if(!is_array($this->foreignColumns)) {
            throw new \yentu\exceptions\SyntaxErrorException("No foreign columns specified for foreign key {$this->name}");
        }
    }

    public function commitNew()
    {
        $this->validate();
        if ($this->name == '' && is_object($this->foreignTable)) {
            $this->name = $this->table->getName() . '_' . implode('_', $this->columns) .
                '_' . $this->foreignTable->getName() .
                '_' . implode('_', $this->foreignColumns) . '_fk';
        } else if ($this->foreignTable === null && $this->nameSet) {
            // Do nothing
        } else if (!is_object($this->foreignTable)) {
            throw new \yentu\exceptions\DatabaseManipulatorException(
                "No references defined for foreign key {$this->name}"
            );
        }

        $this->getDriver()->addForeignKey($this->buildDescription());
    }

    public function name($name)
    {
        if($this->getDriver()->doesForeignKeyExist([
            'schema' => $this->table->getSchema()->getName(),
            'table' => $this->table->getName(),
            'name' => $name
        ])) {
            $this->setKeyDetails($name);
            $this->new = false;
        }
        $this->name = $name;
        $this->nameSet = true;
        return $this;
    }
    
    private function setKeyDetails($name)
    {
        $foreignKey = $this->getDriver()
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

    protected function buildDescription()
    {
        if($this->foreignTable === null) {
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
