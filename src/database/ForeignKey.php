<?php
namespace yentu\database;

class ForeignKey extends DatabaseItem
{
    private $table;
    private $columns;
    
    public function __construct($columns, $table) 
    {
        $this->table = $table;
        $this->columns = $columns;
    }
    
    public function references()
    {
        $args = func_get_args();
        $table = array_shift($args);
        $this->getDriver()->addForeignKey(
            array(
                'columns' => $this->columns,
                'table' => $this->table->getName(),
                'schema' => $this->table->getSchema()->getName(),
                'foreign_columns' => $args,
                'foreign_table' => $table->getName(),
                'foreign_schema' => $table->getSchema()->getName()
            )
        );
        return $this->table;
    }
}