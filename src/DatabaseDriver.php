<?php
namespace yentu;

abstract class DatabaseDriver{
    abstract public function createTable();
    abstract public function describe();
    abstract protected function connect($params);
    
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
