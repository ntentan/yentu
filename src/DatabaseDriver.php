<?php
namespace yentu;

abstract class DatabaseDriver
{
    abstract public function createTable();
    abstract public function describe();
    abstract protected function connect($params);
    
    abstract public function addSchema($name);
    abstract public function dropSchema($name);
    abstract public function addTable($name, $schema);
    abstract public function dropTable($name, $schema);
    
    public function __construct($params) 
    {
        unset($config['driver']);
        $this->connect($params);
    }
    
    public static function getConnection()
    {
        require "yentu/config/default.php";
        $class = "\\yentu\\drivers\\" . ucfirst($config['driver']);
        return new $class($config);
    }
}
