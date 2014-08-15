<?php
namespace yentu;

abstract class DatabaseDriver
{
    private $description;
    
    public function __construct($params) 
    {
        unset($config['driver']);
        $this->connect($params);
        $this->description = $this->describe();     
        
        foreach($this->description['schemata'] as $schemaName => $schema)
        {
            foreach($schema['tables'] as $tableName => $table)
            {
                $this->description['schemata'][$schemaName]['tables'][$tableName]['flat_foreign_keys'] = $this->flattenColumns($table['foreign_keys'], 'columns');
                $this->description['schemata'][$schemaName]['tables'][$tableName]['flat_unique_keys'] = $this->flattenColumns($table['unique_keys']);
            }
        }
    }
    
    abstract protected function describe();
    abstract protected function connect($params);
    
    abstract public function addSchema($name);
    abstract public function dropSchema($name);
    abstract public function addTable($details);
    abstract public function dropTable($details);
    abstract public function addColumn($details);
    abstract public function addPrimaryKey($details);
    abstract public function addUniqueKey($details);   
    abstract public function dropUniqueKey($details);
    abstract public function addAutoPrimaryKey($details);
    abstract public function addForeignKey($details);
    abstract public function dropForeignKey($details);

    /*abstract public function doesSchemaExist($name) **;
    abstract public function doesTableExist($details) **;
    abstract public function doesColumnExist($details) **;
    abstract public function doesForeignKeyExist($details) **;
    abstract public function doesUniqueKeyExist($details);
    abstract public function doesPrimaryKeyExist($details);*/
    
    public function doesSchemaExist($name)
    {
        return isset($this->description['schemata'][$name]);
    }
    
    public function doesTableExist($details)
    {
        return $details['schema'] === false ? isset($this->description['tables'][$details['name']]) : 
            isset($this->description['schemata'][$details['schema']]['tables'][$details['name']]);
    }
    
    public function doesColumnExist($details)
    {
        $table = $this->getTableDetails($details['schema'], $details['table']);
        return isset($table[$details['name']]);
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
    
    private function getTableDetails($schema, $table)
    {
        return $schema === false ? $this->description['tables'][$table] : 
            $this->description['schemata'][$schema]['tables'][$table];        
    }
    
    private function flattenColumns($items, $key = false)
    {
        $flattened = array();
        foreach($items as $name => $item)
        {
            foreach($key === false ? $item : $item[$key] as $column)
            {
                $flattened[$column] = $name;
            }
        }
        return $flattened;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public static function getConnection($config = '')
    {
        if($config == '')
        {
            require "yentu/config/default.php";
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
}
