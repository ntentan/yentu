<?php
namespace yentu;

class ChangeLogger
{
    private $driver;
    private static $version;
    private static $migration;
    private static $session;
    private static $changes;
    private $skippedItemTypes = array();
    private $allowedItemTypes = array();
    private $assertor;    
    
    public function skip($itemType)
    {
        $this->skippedItemTypes[] = $itemType;
    }
    
    public function allowOnly($itemType)
    {
        $this->allowedItemTypes[] = $itemType;
    }    

    private function __construct(DatabaseManipulator $driver) 
    {
        $this->driver = $driver;
        $this->assertor = $driver->getAssertor();        
        if(!$this->assertor->doesTableExist(array('name' => 'yentu_history')))
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
        if(preg_match("/^(?<command>add|drop|change)(?<item_type>[a-zA-Z]+)/", $method, $matches))
        {
            if(
                array_search($matches['item_type'], $this->skippedItemTypes) !== false || 
                (array_search($matches['item_type'], $this->allowedItemTypes) === false && count($this->allowedItemTypes) > 0)
            )
            {
                Yentu::out("S");
                Yentu::out("kipping " . preg_replace("/([a-z])([A-Z])/", "$1 $2", $matches['item_type']) . " '" . $arguments[0]['name'] . "'\n", Yentu::OUTPUT_LEVEL_2);
            }
            else
            {        
                Yentu::announce($matches['command'], $matches['item_type'], $arguments[0]);  
                $return = $this->driver->$method($arguments[0]);
                $outputLevel = Yentu::getOutputLevel(); Yentu::setOutputLevel(Yentu::OUTPUT_LEVEL_0);
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
                Yentu::setOutputLevel($outputLevel);
                self::$changes++;
            }
        }
        else if(preg_match("/^does([A-Za-z]+)/", $method))
        {
            $invokable = new \ReflectionMethod($this->assertor, $method);
            return $invokable->invokeArgs($this->assertor, $arguments);
        }
        else
        {
            $invokable = new \ReflectionMethod($this->driver, $method);
            return $invokable->invokeArgs($this->driver, $arguments);
        }
        
        return $return;
    }
    
    public static function getChanges()
    {
        return self::$changes;
    }
}

