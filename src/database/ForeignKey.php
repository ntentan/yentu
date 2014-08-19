<?php
namespace yentu\database;

class ForeignKey extends DatabaseItem
{
    private $table;
    private $columns;
    private $foreignTable;
    private $foreignColumns;
    private $name;
    private $onDelete = 'NO ACTION';
    private $onUpdate = 'NO ACTION';
    
    public function __construct($columns, $table) 
    {
        $this->table = $table;
        $this->columns = $columns;
        
        // Prevent the committing of the foreign key even if the context
        // switches
        
        $constraint = $this->getDriver()->doesForeignKeyExist(array(
            'schema' => $table->getSchema()->getName(),
            'table' => $table->getName(),
            'columns' => $columns
        ));
        
        if($constraint === false)
        {
            $this->new = true;
        }
        else
        {
            $this->name = $constraint;
        }
    }
    
    public function references($table)
    {
        $this->foreignTable = $table;
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

    public function commitNew() 
    {
        if($this->name == '')
        {
            $this->name = $this->table->getName() . '_' . implode('_', $this->columns) . 
                '_' . $this->foreignTable->getName() . 
                '_'. implode('_', $this->foreignColumns) . '_fk';
        }

        $this->getDriver()->addForeignKey(
            array(
                'columns' => $this->columns,
                'table' => $this->table->getName(),
                'schema' => $this->table->getSchema()->getName(),
                'foreign_columns' => $this->foreignColumns,
                'foreign_table' => $this->foreignTable->getName(),
                'foreign_schema' => $this->foreignTable->getSchema()->getName(),
                'name' => $this->name,
                'on_delete' => $this->onDelete,
                'on_update' => $this->onUpdate
            )
        );        
    }
    
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function onDelete($onDelete)
    {
        $this->onDelete = $onDelete;
        return $this;
    }

    public function onUpdate($onUpdate)
    {
        $this->onUpdate = $onUpdate;
        return $this;
    }
}
