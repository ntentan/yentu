<?php
namespace yentu;

abstract class SchemaDescriptor
{
    /**
     *
     * @var \yentu\DatabaseDriver
     */
    protected $driver;
    
    public function __construct($driver)
    {
        $this->driver = $driver;
    }
    
    abstract public function getSchemata();
    abstract public function getTables();
    
    public function describe()
    {
        $defaultSchema = $this->driver->getDefaultSchema();
        $description = array(
            'schemata' => array(),
        );
        
        $schemata = $this->getSchemata();/*$this->driver->query(
            "select schema_name as name from information_schema.schemata 
            where schema_name not like 'pg_temp%' and 
            schema_name not like 'pg_toast%' and 
            schema_name not in ('pg_catalog', 'information_schema')"
        );*/
        
        foreach($schemata as $schema)
        {
            if($schema['name'] == $defaultSchema)
            {
                $description['tables'] = $this->describeTables($defaultSchema);
                $description['views'] = $this->describeViews($defaultSchema);
            }
            else
            {
                $description['schemata'][$schema['name']]['name'] = $schema['name'];
                $description['schemata'][$schema['name']]['tables'] = $this->describeTables($schema['name']);                
                $description['schemata'][$schema['name']]['views'] = $this->describeViews($schema['name']);                
            }
        }
        
        return self::wrap($description);        
    }
    
    public function describeTables($schema)
    {
        $description = array();
        $tables = $this->getTables();/*$this->driver->query(
            "select table_schema as schema, table_name as name 
            from information_schema.tables
            where table_schema = '$schema' and table_type = 'BASE TABLE'"
        );*/
        
        foreach($tables as $table)
        {
            $table['columns'] = $this->getColumns($table);
            $table['primary_key'] = $this->getPrimaryKey($table); //Constraint($table, 'PRIMARY KEY');
            $table['unique_keys'] = $this->getUniqueKeys($table); //Constraint($table, 'UNIQUE');
            $table['foreign_keys'] = $this->getForeignKeys($table);
            $table['indices'] = $this->getIndices($table);
            $table['schema'] = $this->fixSchema($table['schema']);
            
            $primaryKey = reset($table['primary_key']);
            if(count($primaryKey) == 1 && substr_count($table['columns'][$primaryKey[0]]['default'], 'nextval'))
            {
                $table['auto_increment'] = true;
                unset($table['columns'][$primaryKey[0]]['default']);
            }
            
            $description[$table['name']] = $table;
        }      
        return $description;        
    }
}

