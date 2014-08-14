<?php
namespace yentu;

class ChangeLogger
{
    private $driver;
    private static $version;
    private static $migration;
    private static $session;

    private function __construct(DatabaseDriver $driver) 
    {
        $this->driver = $driver;
    }
    
    public static function wrap($item)
    {
        self::$session = sha1(rand() . time());
        return new ChangeLogger($item);
    }
    
    public static function setVersion($version)
    {
        self::$version = $version;
    }
    
    public static function setMigration($migration)
    {
        self::$migration = $migration;
    }
    
    public function __call($method, $arguments) 
    {
        $reflection = new \ReflectionMethod($this->driver, $method);
        $return = $reflection->invokeArgs($this->driver, $arguments);
        
        if(preg_match("/^(add|drop)/", $method))
        {
            $this->driver->query(
                'INSERT INTO yentu_history(session, version, method, arguments, migration) VALUES (?,?,?,?,?)',
                array(
                    self::$session,
                    self::$version,
                    $method,
                    json_encode($arguments),
                    self::$migration
                )
            );
        }
        
        return $return;
    }
}

