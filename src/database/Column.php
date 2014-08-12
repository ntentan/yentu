<?php
namespace yentu\database;

class Column extends DatabaseItem
{
    private $name;
    private $type;
    private $table;
    private $nulls;
    private $default;
    
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
        if(!$this->getDriver()->doesColumnExist(
            array(
                'table' => $table->getName(),
                'schema' => $table->getSchema()->getName(),
                'name' => $name
            )
        )){
            $this->new = true;
        }
    }
    
    public function type($type)
    {
        $this->type = $type;
        return $this;
    }
    
    public function nulls($nulls)
    {
        $this->nulls = $nulls;
        return $this;
    }
    
    public function defaultValue($default)
    {
        $this->default = $default;
        return $this;
    }

    public function commit() 
    {
        $columnDescription = $this->buildColumnDescription();
        if($this->isNew())
        {
            $this->getDriver()->addColumn($columnDescription);        
        }
        else
        {
            $this->getDriver()->setColumnNulls($columnDescription);
        }
    }

}

