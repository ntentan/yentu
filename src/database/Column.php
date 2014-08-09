<?php
namespace yentu\database;

class Column extends DatabaseItem
{
    private $name;
    private $type;
    private $table;
    private $nulls;
    private $added = false;
    
    private function buildColumnDescription()
    {
        return array(
            'name' => $this->name,
            'type' => $this->type,
            'table' => $this->table->getName(),
            'schema' => $this->table->getSchema()->getName(),
            'nulls' => $this->nulls
        );
    }
    
    public function __construct($name, $table)
    {
        $this->table = $table;
        $this->name = $name;
        $this->setEncapsulated($table);
    }
    
    public function type($type)
    {
        $this->type = $type;
        $this->table->getDriver()->addColumn(
            $this->buildColumnDescription()
        );
        $this->added = true;
        return $this;
    }
    
    public function nulls($nulls)
    {
        $this->nulls = $nulls;
        if($this->added)
        {
            $this->table->getDriver()->setColumnNulls(
                $this->buildColumnDescription()
            );
        }
        return $this;
    }
    
    public function defaultValue()
    {
        return $this;
    }
}

