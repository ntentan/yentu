<?php
namespace yentu\database;

class Table
{
    private $schema;
    private $name;
    private $driver;
    
    public function __construct($name, $driver, $schema = false) 
    {
        $this->name = $name;
        $this->driver = $driver;
        $this->schema = $schema;
        $driver->addTable($name, $schema);
    }
    
    public function table($name)
    {
        return new Table($name, $this->driver, $this->schema);
    }
}
