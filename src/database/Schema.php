<?php

namespace yentu\database;

class Schema extends DatabaseItem
{
    private $name;
    
    public function __construct($name)
    {
        $this->name = $name;
        if(!$this->getDriver()->doesSchemaExist($name))
        {        
            $this->getDriver()->addSchema($name);
        }
    }
    
    public function table($name)
    {
        return DatabaseItem::create('table', $name, $this);
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function commit() {
        
    }
}

