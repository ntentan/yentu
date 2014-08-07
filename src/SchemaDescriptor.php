<?php
namespace yentu;

abstract class SchemaDescriptor
{
    protected $driver;
    
    public function __construct($driver)
    {
        $this->driver = $driver;
    }
    
    abstract public function describe();
}

