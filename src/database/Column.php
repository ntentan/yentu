<?php
namespace yentu\database;

use yentu\Yentu;

class Column extends DatabaseItem
{
    private $name;
    private $type;
    private $table;
    private $nulls;
    private $default;
    private $length;
    
    protected function buildDescription()
    {
        return array(
            'name' => $this->name,
            'type' => $this->type,
            'table' => $this->table->getName(),
            'schema' => $this->table->getSchema()->getName(),
            'nulls' => $this->nulls,
            'length' => $this->length
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
       
        if(!$this->isNew())
        {
            $this->addChange('nulls', $nulls);
        }
        
        $this->nulls = $nulls;
        return $this;
    }
    
    public function defaultValue($default)
    {
        $this->default = $default;
        return $this;
    }

    public function commitNew() 
    {
        $this->getDriver()->addColumn($this->buildDescription());        
    }
    
    public function length($length)
    {
        $this->length = $length;
        return $this;
    }
}

