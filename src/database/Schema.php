<?php

namespace yentu\database;

class Schema extends DatabaseItem
{
    private $name;
    
    public function __construct($name)
    {
        $this->name = $name;
        $this->getDriver()->addSchema($name);
    }
    
    public function table($name)
    {
        return new Table($name, $this);
    }
    
    public function getName()
    {
        return $this->name;
    }
}

