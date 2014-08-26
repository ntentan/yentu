<?php
namespace yentu\database;


class Index extends \yentu\database\DatabaseItem
{
    private $name;
    private $unique = false;
    private $columns;
    private $table;
    
    public function __construct($columns, $table) 
    {
        $this->columns = $columns;
        $this->table = $table;
        $name = $this->getDriver()->doesForeignKeyExist(array(
            'schema' => $table->getSchema()->getName(),
            'table' => $table->getName(),
            'columns' => $columns
        ));
        if($name === false)
        {
            $this->new = true;
        }
        else
        {
            $this->name = $name;
        }
    }
    
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function unique($unique = true)
    {
        $this->unique = $unique;
        return $this;
    }
    
    public function commitNew() 
    {
        $this->getDriver()->addIndex(
            array(
                'table' => $this->table->getName(),
                'schema' => $this->table->getSchema()->getName(),
                'columns' => $this->columns,
                'name' => $this->name
            )
        );
    }
}