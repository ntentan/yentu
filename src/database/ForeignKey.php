<?php
namespace yentu\database;

class ForeignKey extends DatabaseItem
{
    private $table;
    private $columns;
    private $foreignTable;
    private $foreignColumns;
    private $name;
    
    public function __construct($columns, $table) 
    {
        $this->table = $table;
        $this->columns = $columns;
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
        $this->getDriver()->dropForeignKey(
            array(
                'columns' => $this->columns,
                'table' => $this
            )
        );
    }

    public function commit() 
    {
        $this->getDriver()->addForeignKey(
            array(
                'columns' => $this->columns,
                'table' => $this->table->getName(),
                'schema' => $this->table->getSchema()->getName(),
                'foreign_columns' => $this->foreignColumns,
                'foreign_table' => $this->foreignTable->getName(),
                'foreign_schema' => $this->foreignTable->getSchema()->getName(),
                'name' => $this->name
            )
        );        
    }
    
    public function name($name)
    {
        $this->name = $name;
    }

}