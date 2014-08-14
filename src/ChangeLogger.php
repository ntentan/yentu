<?php
namespace yentu;

class ChangeLogger
{
    private $driver;
    private $version;
    private $migration;


    private function __construct(DatabaseDriver $driver) 
    {
        $this->driver = $driver;
    }
    
    public static function wrap($item)
    {
        return new ChangeLogger($item);
    }
    
    public function setVersion($version)
    {
        $this->version = $version;
    }
    
    public function setMigration($migration)
    {
        $this->migration = $migration;
    }
    
    public function __call($method, $arguments) 
    {
        $reflection = new \ReflectionMethod($this->driver, $method);
        $return = $reflection->invokeArgs($this->driver, $arguments);
        
        if(preg_match("/^(add|drop)/", $method))
        {
            $this->driver->query(
                'INSERT INTO yentu_history(version, method, arguments, migration) VALUES (?,?,?,?)',
                array(
                    $this->version,
                    $method,
                    json_encode($arguments),
                    $this->migration
                )
            );
        }
        
        return $return;
    }
}

