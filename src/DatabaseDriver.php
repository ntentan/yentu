<?php
namespace yentu;

abstract class DatabaseDriver
{
    private $description;
    
    public function __construct($params) 
    {
        $this->connect($params);
        $this->description = $this->describe(); 
    }
    
    public function __call($name, $arguments)
    {
        if(preg_match("/^(add|drop|change)/", $name))
        {
            $this->description->$name($arguments[0]);
            $name = "_$name";
            $this->$name($arguments[0]);
        }
    }
        
    abstract protected function describe();
    abstract protected function connect($params);
    
    abstract protected function _addSchema($name);
    abstract protected function _dropSchema($name);
    abstract protected function _addTable($details);
    abstract protected function _dropTable($details);
    abstract protected function _addColumn($details);
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
    abstract protected function _changeColumnNulls($details);
    
    protected function dropItem($details, $type)
    {
        unset($this->description['schemata'][$details['schema']]['tables'][$details['table']][$type][$details['name']]);
        foreach($details['columns'] as $column)
        {
            unset($this->description['schemata'][$details['schema']]['tables'][$details['table']]["flat_$type"][$column]);
        }
    }
    
    public function doesSchemaExist($name)
    {
        return isset($this->description['schemata'][$name]);
    }
    
    public function doesTableExist($details)
    {
        if(is_string($details))
        {
            $details = array(
                'schema' => false,
                'name' => $details
            );
        }
        
        return $details['schema'] == false? 
            isset($this->description['tables'][$details['name']]) : 
            isset($this->description['schemata'][$details['schema']]['tables'][$details['name']]);
    }
    
    public function doesColumnExist($details)
    {
        $table = $this->getTableDetails($details['schema'], $details['table']);
        return isset($table['columns'][$details['name']]);
    }
    
    private function doesItemExist($details, $type)
    {
        $table = $this->getTableDetails($details['schema'], $details['table']);
        if(isset($details['columns']))
        {
            return isset($table["flat_$type"][$details['columns'][0]]) ? $table["flat_$type"][$details['columns'][0]] : false;
        }
        else if(isset($details['name']))
        {
            return isset($table[$type][$details['name']]);
        }        
    }
    
    public function doesForeignKeyExist($details)
    {
        return $this->doesItemExist($details, 'foreign_keys');
    }
    
    public function doesUniqueKeyExist($details)
    {
        return $this->doesItemExist($details, 'unique_keys');
    }
    
    public function doesPrimaryKeyExist($details)
    {
        return $this->doesItemExist($details, 'primary_key');
    } 
    
    public function doesIndexExist($details)
    {
        return $this->doesItemExist($details, 'indices');
    }
    
    private function getTableDetails($schema, $table)
    {
        return $schema === false ? $this->description['tables'][$table] : 
            $this->description['schemata'][$schema]['tables'][$table];        
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
    }    
}
