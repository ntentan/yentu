<?php
namespace yentu;

abstract class DatabaseDriver
{
    private $description = false;
    abstract protected function describe();
    abstract protected function connect($params);
    
    abstract public function addSchema($name);
    abstract public function dropSchema($name);
    abstract public function addTable($details);
    abstract public function dropTable($details);
    abstract public function addColumn($details);
    abstract public function addPrimaryKey($details);
    abstract public function addUniqueConstraint($details);    
    abstract public function addAutoPrimaryKey($details);
    abstract public function addForeignKey($details);
    abstract public function dropForeignKey($details);
    
    abstract public function doesSchemaExist($name);
    abstract public function doesTableExist($details);
    abstract public function doesColumnExist($details);
    abstract public function doesForeignKeyExist($details);
    
    public function getDescription()
    {
        if($this->description === false)
        {
            $this->description = $this->describe();
        }
        return $this->description;
    }

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
        $this->query('INSERT INTO yentu_history(version) values (?)', array($version));
    }
    
    public function getVersion() 
    {
        $version = $this->query("SELECT MAX(version) as version FROM yentu_history");
        return $version[0]['version'];
    }
    
    public function getLastSession()
    {
        $session = $this->query("SELECT session FROM yentu_history ORDER BY version DESC LIMIT 1");
        return $session[0]['session'];
    }
}
