<?php

namespace yentu\database;

abstract class DatabaseItem 
{
    //private $encapsulated;
    private static $encapsulated = array();
    private static $canCommitPending = true;
    
    /**
     *
     * @var \yentu\DatabaseDriver
     */
    private static $driver;
    protected $new = false;
    private $changes = array();


    private static $itemTypes = array(
        'table' => 'Table',
        'schema' => 'Schema',
        'column' => 'Column',
        'foreign_key' => 'ForeignKey',
        'primary_key' => 'PrimaryKey',
        'unique_key' => 'UniqueKey',
        'index' => 'Index'
    );
    
    protected function addChange($method, $args)
    {
        $this->changes[] = array(
            'method' => $method,
            'args' => $args
        );
    }
    
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
        $encapsulated = end(self::$encapsulated);
        
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
        if(self::$canCommitPending === FALSE) return;
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
    
    public static function disableCommitPending()
    {
        self::$canCommitPending = false;
    }
    
    public static function enableCommitPending()
    {
        self::$canCommitPending = true;
    }   
    
    public function commit()
    {
        if($this->isNew())
        {
            $this->commitNew();
        }
        else
        {
            foreach($this->changes as $change)
            {
                /*$method = new \ReflectionMethod(self::$driver, $change['method']);
                $method->invoke(self::$driver, $change['args']);*/
                self::$driver->$change['method']($change['args']);
            }
        }
        return $this;
    }
    
    abstract public function commitNew();
}

