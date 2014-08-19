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
        if(!$this->driver->doesTableExist(array('name' => 'yentu_history')))
        {
            $this->driver->createHistory();
        }
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
        /*$reflection = new \ReflectionMethod($this->driver, $method);
        $return = $reflection->invokeArgs($this->driver, $arguments);*/
        
        $return = $this->driver->$method($arguments[0]);
        
        if(preg_match("/^(add|drop|change)/", $method))
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

