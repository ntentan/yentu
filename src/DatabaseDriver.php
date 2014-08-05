<?php
namespace yentu;

abstract class Driver{
    abstract public function query();
    abstract public function describe();
    abstract protected function connect($params);
    
    public static function getConnection()
    {
        require "yentu/config/default.php";
        $class = "\\yentu\\drivers\\" . ucfirst($driver);
        return new $class($config);
    }
}
