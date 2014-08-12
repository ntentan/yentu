<?php

namespace yentu\database;

abstract class DatabaseItem 
{
    //private $encapsulated;
    private static $encapsulated = array();
    private static $top;
    private static $driver = false;
    protected $new = false;
    
    private static $itemTypes = array(
        'table' => 'Table',
        'schema' => 'Schema',
        'column' => 'Column',
        'foreign_key' => 'ForeignKey',
        'primary_key' => 'PrimaryKey',
        'unique_key' => 'UniqueKey'
    );
    
    public function isNew()
    {
        return $this->new;
    }
    
    /**
     * 
     * @return \yentu\DatabaseDriver
     */
    protected function getDriver()
    {
        return self::$driver;
    }
    
    public static function setDriver($driver)
    {
        self::$driver = $driver;
    }
    
    public static function push($encapsulated)
    {
        self::$encapsulated[] = $encapsulated;
    }
    
    public function __call($method, $arguments)
    {
        var_dump(count(self::$encapsulated));
        $encapsulated = end(self::$encapsulated);
        $classy = new \ReflectionClass($encapsulated);
        var_dump($classy->getName(), $method);
        
        if(!is_object($encapsulated))
        {
            throw new \Exception("Failed to call method {$method}. Could not find an encapsulated object.");
        }
        else if (method_exists($encapsulated, $method))
        {
            $method = new \ReflectionMethod($encapsulated, $method);
            $this->commit();
            return $method->invokeArgs($encapsulated, $arguments);
        }
        else
        {
            $encapsulated = array_pop(self::$encapsulated);
            return $encapsulated->__call($method, $arguments);
        }
    }
    
    public static function commitPending()
    {
        $size = count(self::$encapsulated);
        for($i = 0; $i < $size; $i++)
        {
            $encapsulated = array_pop(self::$encapsulated);
            $encapsulated->commit();
        }
    }
    
    public static function create($type)
    {
        $args = func_get_args();
        $type = array_shift($args);
        $class = new \ReflectionClass("\\yentu\\database\\" . self::$itemTypes[$type]);
        $item = $class->newInstanceArgs($args);
        DatabaseItem::push($item);
        return $item;
    }
    
    abstract public function commit();
}

