<?php
namespace yentu;

abstract class DatabaseDriver
{
    private $description;
    private $assertor;
    private $skippedItemTypes = array();
    private $allowedItemTypes = array();
    
    public function __construct($params) 
    {
        $this->connect($params);
        $this->description = $this->describe(); 
        $this->assertor = new DatabaseAssertor($this->description);
    }
    
    public function skip($itemType)
    {
        $this->skippedItemTypes[] = $itemType;
    }
    
    public function allowOnly($itemType)
    {
        
    }
    
    public function __call($name, $arguments)
    {
        if(preg_match("/^(?<command>add|drop|change)(?<item_type>[a-zA-Z]+)/", $name, $matches))
        {
            if(
                array_search($matches['item_type'], $this->skippedItemTypes) || 
                (!array_search($matches['item_type'], $this->allowedItemTypes) && count($this->allowedItemTypes) > 0)
            )
            {
                Yentu::out("Skipping " . preg_replace("/([a-z])([A-Z])/", "$1 $2", $matches['iten_type']) . " '" . $arguments['name'] . "'\n");
            }
            else
            {
                Yentu::announce($matches['command'], $matches['item_type'], $arguments[0]);
                $this->description->$name($arguments[0]);
                $name = "_$name";
                new \ReflectionMethod($this, $name);
                $this->$name($arguments[0]);
            }
        }
        else if(preg_match("/^does([A-Za-z]+)/", $name))
        {
            $method = new \ReflectionMethod($this->assertor, $name);
            return $method->invokeArgs($this->assertor, $arguments);
        }
    }
        
    abstract protected function describe();
    abstract protected function connect($params);
    
    abstract protected function _addSchema($name);
    abstract protected function _dropSchema($name);
    abstract protected function _addTable($details);
    abstract protected function _dropTable($details);
    abstract protected function _addColumn($details);
    abstract protected function _changeColumnNulls($details);
    abstract protected function _changeColumnName($details);
    abstract protected function _dropColumn($details);
    abstract protected function _addPrimaryKey($details);
    abstract protected function _dropPrimaryKey($details);
    abstract protected function _addUniqueKey($details);   
    abstract protected function _dropUniqueKey($details);
    abstract protected function _addAutoPrimaryKey($details);
    abstract protected function _dropAutoPrimaryKey($details);
    abstract protected function _addForeignKey($details);
    abstract protected function _dropForeignKey($details);
    abstract protected function _addIndex($details);
    abstract protected function _dropIndex($details);
    abstract protected function _addView($details);
    abstract protected function _dropView($details);
    abstract protected function _changeViewDefinition($details);
        
    protected function dropTableItem($details, $type)
    {
        unset($this->description['schemata'][$details['schema']]['tables'][$details['table']][$type][$details['name']]);
        foreach($details['columns'] as $column)
        {
            unset($this->description['schemata'][$details['schema']]['tables'][$details['table']]["flat_$type"][$column]);
        }
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public static function getConnection($config = '')
    {
        if($config == '')
        {
            require Yentu::getPath("config/default.php");
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
    
    public function createHistory()
    {
        $level = Yentu::getOutputLevel();
        Yentu::setOutputLevel(Yentu::OUTPUT_LEVEL_0);
        $this->addTable(array('name' => 'yentu_history'));
        
        $this->addColumn(
            array(
                'table' => 'yentu_history',
                'name' => 'session',
                'type' => 'string'
            )
        );
        
        $this->addColumn(
            array(
                'table' => 'yentu_history',
                'name' => 'version',
                'type' => 'string'
            )
        );     
        
        $this->addColumn(
            array(
                'table' => 'yentu_history',
                'name' => 'method',
                'type' => 'string'
            )
        );  
        
        $this->addColumn(
            array(
                'table' => 'yentu_history',
                'name' => 'arguments',
                'type' => 'text'
            )
        );  
        
        $this->addColumn(
            array(
                'table' => 'yentu_history',
                'name' => 'migration',
                'type' => 'string'
            )
        ); 
        
        $this->addColumn(
            array(
                'table' => 'yentu_history',
                'name' => 'id',
                'type' => 'integer'
            )
        );
        
        $this->addPrimaryKey(
            array(
                'table' => 'yentu_history',
                'name' => 'yentu_history_pk',
                'columns' => array('id')
            )
        );
        
        $this->addAutoPrimaryKey(
            array(
                'table' => 'yentu_history',
                'column' => 'id'
            )
        );
        Yentu::setOutputLevel($level);
    }    
}
