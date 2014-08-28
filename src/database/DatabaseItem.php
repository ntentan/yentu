<?php

namespace yentu\database;

abstract class DatabaseItem 
{
    //private $encapsulated;
    //private static $encapsulated = array();
    //private static $canCommitPending = true;
    
    private $encapsulated;
    private static $encapsulatedStack = array();
    
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
        self::$encapsulatedStack[] = $encapsulated;
    }
    
    public function __call($method, $arguments)
    {   
        if(!is_object($this->encapsulated))
        {
            $class = new \ReflectionClass($this);
            var_dump($class->getName());
            throw new \Exception("Failed to call method {$method}. Could not find an encapsulated object.");
        }
        else if (method_exists($this->encapsulated, $method))
        {
            $method = new \ReflectionMethod($this->encapsulated, $method);
            $this->commit();
            array_pop(self::$encapsulatedStack);
            
            return $method->invokeArgs($this->encapsulated, $arguments);
        }
        else
        {
            $this->commit();
            array_pop(self::$encapsulatedStack);
            
            return $this->encapsulated->__call($method, $arguments);
        }
        /*else
        {
            $encapsulated = array_pop(self::$encapsulated);
            return $encapsulated->__call($method, $arguments);
        }*/
    }
    
    /*public static function commitPending()
    {
        //if(self::$canCommitPending === FALSE) return;
        //$size = count(self::$encapsulated);
        for($i = 0; $i < $size; $i++)
        {
            $encapsulated = array_pop(self::$encapsulated);
            $encapsulated->commit();
        }
    }*/
    
    public function setEncapsulated($item)
    {
        $this->encapsulated = $item;
    }
    
    public static function purge()
    {
        for($i = 0; $i < count(self::$encapsulatedStack); $i++)
        {
            $item = array_pop(self::$encapsulatedStack);
            $item->commit();
        }
    }
    
    public function create()
    {
        $args = func_get_args();
        $type = array_shift($args);
        $class = new \ReflectionClass("\\yentu\\database\\" . self::$itemTypes[$type]);
        $item = $class->newInstanceArgs($args);
        $item->setEncapsulated($this);
        self::push($item);
        return $item;
    }
    
    /*public static function disableCommitPending()
    {
        self::$canCommitPending = false;
    }
    
    public static function enableCommitPending()
    {
        self::$canCommitPending = true;
    }*/  
    
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
                self::$driver->$change['method']($change['args']);
            }
        }
        return $this;
    }
    
    abstract public function commitNew();
}

