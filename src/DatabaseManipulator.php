<?php
namespace yentu;

use clearice\ClearIce;
use ntentan\config\Config;

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
        $this->connection = \ntentan\atiaa\Db::getConnection($config);
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
        if(preg_match("/^(add|drop|change|executeQuery|reverseQuery)/", $name))
        {
            $details = Parameters::wrap($arguments[0]);
            $this->description->$name($details);
            $name = "_$name";
            new \ReflectionMethod($this, $name);
            return $this->$name($details);
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
        catch(\ntentan\atiaa\exceptions\DatabaseDriverException $e)
        {
            throw new exceptions\DatabaseManipulatorException($e->getMessage());
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
    abstract protected function _changeTableName($details);
    abstract protected function _addColumn($details);
    abstract protected function _changeColumnNulls($details);
    abstract protected function _changeColumnName($details);
    abstract protected function _changeColumnDefault($details);
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
    
    protected function _changeForeignKeyOnDelete($details)
    {
        $this->_dropForeignKey($details['from']);
        $this->_addForeignKey($details['to']);
    }
    
    protected function _changeForeignKeyOnUpdate($details)
    {
        $this->_dropForeignKey($details['from']);
        $this->_addForeignKey($details['to']);
    }
    
    protected function _executeQuery($details)
    {
        $this->query($details['query'], $details['bind']);
    }
    
    protected function _reverseQuery($details)
    {
        
    }
    
    abstract public function convertTypes($type, $direction, $length);
    
    /**
     * 
     * @return SchemaDescription
     */
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
    public static function create()
    {
        $config = Config::get('yentu:default.db');
        if($config['driver'] == '')
        {
            throw new exceptions\DatabaseManipulatorException("Please specify a database driver.");
        }        
        $class = "\\yentu\\manipulators\\" . ucfirst($config['driver']);
        unset($config['variables']);
        return new $class($config);
    }
    
    public function setVersion($version)
    {
        $this->query('INSERT INTO yentu_history(version) values (?)', array($version));
    }
    
    public function getVersion() 
    {
        $version = $this->query("SELECT MAX(version) as version FROM yentu_history");
        return isset($version[0]) ? $version[0]['version'] : null;
    }
    
    public function getLastSession()
    {
        $session = $this->query("SELECT session FROM yentu_history ORDER BY id DESC LIMIT 1");
        return isset($session[0]['session']) ? $session[0]['session'] : null;
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
        catch(\ntentan\atiaa\exceptions\TableNotFoundException $e)
        {
            ClearIce::pushOutputLevel(ClearIce::OUTPUT_LEVEL_0);
            $this->addTable(array('schema' => '', 'name' => 'yentu_history'));

            $this->addColumn(array('default' => null, 'schema' => '', 'nulls'=> true, 'length' => null, 'table' => 'yentu_history', 'name' => 'session', 'type' => 'string'));
            $this->addColumn(array('default' => null, 'schema' => '', 'nulls'=> false, 'length' => null, 'table' => 'yentu_history', 'name' => 'version', 'type' => 'string'));     
            $this->addColumn(array('default' => null, 'schema' => '', 'nulls'=> true, 'length' => null, 'table' => 'yentu_history', 'name' => 'method', 'type' => 'string'));  
            $this->addColumn(array('default' => null, 'schema' => '', 'nulls'=> true, 'length' => null, 'table' => 'yentu_history', 'name' => 'arguments', 'type' => 'text'));  
            $this->addColumn(array('default' => null, 'schema' => '', 'nulls'=> true, 'length' => null, 'table' => 'yentu_history', 'name' => 'migration', 'type' => 'string')); 
            $this->addColumn(array('default' => null, 'schema' => '', 'nulls'=> true, 'length' => null, 'table' => 'yentu_history', 'name' => 'default_schema', 'type' => 'string')); 
            $this->addColumn(array('default' => null, 'schema' => '', 'nulls'=> true, 'length' => null, 'table' => 'yentu_history', 'name' => 'id', 'type' => 'integer'));
            $this->addPrimaryKey(array('schema' => '', 'table' => 'yentu_history', 'name' => 'yentu_history_pk', 'columns' => array('id')));
            $this->addAutoPrimaryKey(array('schema' => '', 'table' => 'yentu_history', 'column' => 'id'));
            ClearIce::popOutputLevel();
        }
    }
    
    public function __clone()
    {
        if(is_object($this->schemaDescription))
        {
            $this->schemaDescription = clone $this->schemaDescription;
        }
    }
}
