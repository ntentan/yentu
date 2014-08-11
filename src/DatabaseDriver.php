<?php
namespace yentu;

abstract class DatabaseDriver
{
    abstract public function describe();
    abstract protected function connect($params);
    
    abstract public function addSchema($name);
    abstract public function dropSchema($name);
    abstract public function addTable($details);
    abstract public function dropTable($details);
    abstract public function addColumn($details);
    abstract public function addPrimaryKey($details);
    abstract public function addUniqueConstraint($details);    
    abstract public function makeAutoPrimaryKey($details);
    abstract public function addForeignKey($details);
    abstract public function doesTableExist($details);

    public function __construct($params) 
    {
        unset($config['driver']);
        $this->connect($params);
    }
    
    public static function getConnection($config = '')
    {
        if($config == '')
        {
            require "yentu/config/default.php";
        }
        $class = "\\yentu\\drivers\\" . ucfirst($config['driver']);
        return new $class($config);
    }
    
    public function setVersion($version)
    {
        $this->query('UPDATE yentu_version SET version = ?', array($version));
    }
    
    public function getVersion() 
    {
        $version = $this->query("SELECT version FROM yentu_version");
        return $version[0]['version'];
    }
}
