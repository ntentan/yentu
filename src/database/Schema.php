<?php

namespace yentu\database;

class Schema
{
    private $name;
    private $driver;
    
    public function __construct($name, $driver)
    {
        $this->name = $name;
        $this->driver = $driver;
        $driver->addSchema($name);
    }
    
    public function table($name)
    {
        return new Table($name, $this->driver, $this);
    }
    
    public function getName()
    {
        return $this->name;
    }
}

