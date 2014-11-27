<?php
namespace yentu;

use clearice\ClearIce;

abstract class DatabaseManipulator
{
    const CONVERT_TO_DRIVER = 'driver';
    const CONVERT_TO_YENTU = 'yentu';
    
    private $schemaDescription;
    private $assertor; 
    private $connection;
    private $dumpQuery;
    private $disableQuery;
    protected $defaultSchema;
    
    public function __construct($config) 
    {
        $this->connection = \ntentan\atiaa\Atiaa::getConnection($config);
    }
    
    public function __get($name)
    {
        if($name === 'description')
        {
            return $this->getDescription();
        }
    }
    
    public function setDumpQuery($dumpQuery)
    {
        $this->dumpQuery = $dumpQuery;
    }
    
    public function setDisableQuery($disableQuery)
    {
        $this->disableQuery = $disableQuery;
    }
        
    public function __call($name, $arguments)
    {
        if(preg_match("/^(add|drop|change)/", $name))
        {
            $this->description->$name($arguments[0]);
            $name = "_$name";
            new \ReflectionMethod($this, $name);
            return $this->$name($arguments[0]);
        }
        else
        {
            throw new \Exception("Failed to execute method '$name'");
        }
    }
    
    public function query($query, $bind = false)
    {
        try{
            if($this->dumpQuery)
            {
                echo "$query\n";
            }
            
            ClearIce::output("\n    > Running Query [$query]", ClearIce::OUTPUT_LEVEL_3);
            
            if($this->disableQuery !== true)
            {
                return $this->connection->query($query, $bind);
            }
        }
        catch(\ntentan\atiaa\DatabaseDriverException $e)
        {
            throw new DatabaseManipulatorException($e->getMessage());
        }
    }
    
    public function disconnect()
    {
        $this->connection->disconnect();
    }
    
    public function getDefaultSchema()
    {
        return $this->connection->getDefaultSchema();
    }
    
    public function getAssertor()
    {
        if(!is_object($this->assertor))
        {
            $this->assertor = new DatabaseAssertor($this->description);
        }
        return $this->assertor;
    }
    
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
    abstract public function convertTypes($type, $direction, $length);
        
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
        if(!is_object($this->schemaDescription))
        {
            $this->schemaDescription = SchemaDescription::wrap($this->connection->describe(), $this);
        }
        return $this->schemaDescription;
    }
    
    /**
     * 
     * @param array<mixed> $config
     * @return \yentu\DatabaseManipulator;
     */
    public static function create($config = '')
    {
        if($config == '')
        {
            require Yentu::getPath("config/default.php");
        }
        
        $class = "\\yentu\\manipulators\\" . ucfirst($config['driver']);
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
    
    public function getSessionVersions($session)
    {
        $sessionVersions = array();
        $versions = $this->query(
            "SELECT DISTINCT version FROM yentu_history WHERE session = ?", array($session)
        );
        
        foreach($versions as $version)
        {
            $sessionVersions[] = $version['version'];
        }
        
        return $sessionVersions;
    }
    
    public function createHistory()
    {
        try{
            $this->connection->describeTable('yentu_history');
        }
        catch(\ntentan\atiaa\DescriptionException $e)
        {
            $level = ClearIce::getOutputLevel();
            ClearIce::setOutputLevel(ClearIce::OUTPUT_LEVEL_0);
            $this->addTable(array('name' => 'yentu_history'));

            $this->addColumn(array('table' => 'yentu_history', 'name' => 'session', 'type' => 'string'));
            $this->addColumn(array('table' => 'yentu_history', 'name' => 'version', 'type' => 'string'));     
            $this->addColumn(array('table' => 'yentu_history', 'name' => 'method', 'type' => 'string'));  
            $this->addColumn(array('table' => 'yentu_history', 'name' => 'arguments', 'type' => 'text'));  
            $this->addColumn(array('table' => 'yentu_history', 'name' => 'migration', 'type' => 'string')); 
            $this->addColumn(array('table' => 'yentu_history', 'name' => 'default_schema', 'type' => 'string')); 
            $this->addColumn(array('table' => 'yentu_history', 'name' => 'id', 'type' => 'integer'));
            $this->addPrimaryKey(array('table' => 'yentu_history', 'name' => 'yentu_history_pk', 'columns' => array('id')));
            $this->addAutoPrimaryKey(array('table' => 'yentu_history', 'column' => 'id'));
            ClearIce::setOutputLevel($level);
        }
    }
}
