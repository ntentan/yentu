<?php
namespace yentu;

class SchemaDescription implements \ArrayAccess
{
    private $description = array(
        'schemata' => array(),
        'tables' => array(),
        'views' => array()
    );
    
    private function __construct($description)
    {
        $this->description = $description;
        $this->flattenAllColumns();
    }
    
    public function offsetExists($offset) 
    {
        return isset($this->description[$offset]);
    }

    public function offsetGet($offset) 
    {
        return $this->description[$offset];
    }

    public function offsetSet($offset, $value) {}
    public function offsetUnset($offset) {}
    
    public function addSchema($name)
    {
        $this->description['schemata'][$name] = array(
            'name' => $name,
            'tables' => array()
        );
    }
    
    public function dropSchema($name)
    {
        unset($this->description['schemata'][$name]);
    }
    
    
    public function addTable($details)
    {
        $table = array(
            'name' => $details['name'],
            'schema' => $details['schema'],
            'columns' => array(),
            'primary_key' => array(),
            'unique_keys' => array(),
            'foreign_keys' => array(),
            'indices' => array()
        );
        $this->setTable(array('schema'=>$details['schema'], 'table'=>$details['name']), $table);
    }
    
    public function addView($details)
    {
        $view = array(
            'name' => $details['name'],
            'definition' => $details['definition']
        );
        
        if($details['schema'] != '')
        {
            $this->description['schemata'][$details['schema']]['views'][$details['name']] = $view;
        }
        else
        {
            $this->description['views'][$details['name']] = $view;
        }
    }
    
    public function dropView($details)
    {
        $this->dropTable($details, 'views');
    }
    
    public function changeViewDefinition($details)
    {
        $viewDetails = array(
            'view' => $details['from']['name'],
            'schema' => $details['from']['schema']
        );
        $view = $this->getTable($viewDetails, 'view');
        $view['definition'] = $details['to']['definition'];
        $this->setTable($viewDetails, $view, 'view');
    }
    
    public function dropTable($details, $type = 'tables')
    {
        if($details['schema'] != '')
        {
            unset($this->description['schemata'][$details['schema']][$type][$details['name']]);
        }
        else
        {
            unset($this->description[$type][$details['name']]);
        }        
    }
    
    public function getTable($details, $type = 'table')
    {
        if($details['schema'] == '')
        {
            return $this->description["{$type}s"][$details[$type]];
        }
        else
        {
            return $this->description['schemata'][$details['schema']]["{$type}s"][$details[$type]];
        }
    }
    
    private function setTable($details, $table, $type = 'table')
    {
        if($details['schema'] == '')
        {
            $this->description["{$type}s"][$details[$type]] = $table;
        }
        else
        {
            $this->description['schemata'][$details['schema']]["{$type}s"][$details[$type]] = $table;
        }
        $this->flattenAllColumns();
    }
    
    private function flattenAllColumns()
    {
        foreach($this->description['schemata'] as $schemaName => $schema)
        {
            foreach($schema['tables'] as $tableName => $table)
            {
                $this->description['schemata'][$schemaName]['tables'][$tableName]['flat_foreign_keys'] = $this->flattenColumns($table['foreign_keys'], 'columns');
                $this->description['schemata'][$schemaName]['tables'][$tableName]['flat_unique_keys'] = $this->flattenColumns($table['unique_keys'], 'columns');
                $this->description['schemata'][$schemaName]['tables'][$tableName]['flat_indices'] = $this->flattenColumns($table['indices'], 'columns');
            }
        }        
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
    
    public function addColumn($details)
    {
        $table = $this->getTable($details);
        $table['columns'][$details['name']] = array(
            'name' => $details['name'],
            'type' => $details['type'],
            'nulls' => $details['nulls']
        );
        $this->setTable($details, $table);
    }
    
    public function dropColumn($details)
    {
        $table = $this->getTable($details);
        unset($table['columns'][$details['name']]);
        $this->setTable($details, $table);
    }    
    
    public function changeColumnNulls($details)
    {
        $table = $this->getTable($details);
        $table['columns'][$details['to']['name']]['nulls'] = $details['to']['nulls'];
        $this->setTable($details, $table);
    }
    
    public function changeColumnName($details)
    {
        $table = $this->getTable($details);
        $table['columns'][$details['to']['name']]['name'] = $details['to']['name'];
        $this->setTable($details, $table);
    }
    
    public function addPrimaryKey($details)
    {
        $table = $this->getTable($details);
        $table['primary_key'] = array(
            $details['name'] => array(
                'columns' => $details['columns']
            )
        );
        $this->setTable($details, $table);
    }
    
    public function dropPrimaryKey($details)
    {
        $table = $this->getTable($details);
        unset($table['primary_key']);
        $this->setTable($details, $table);
    }
    
    public function addAutoPrimaryKey($details)
    {
        $table = $this->getTable($details);
        $table['auto_increment'] = true;
        $this->setTable($details, $table);
    }
    
    public function dropAutoPrimaryKey($details)
    {
        $table = $this->getTable($details);
        $table['auto_increment'] = false;
        $this->setTable($details, $table);
    }    
    
    public function addUniqueKey($details)
    {
        $table = $this->getTable($details);
        $table['unique_keys'][$details['name']]['columns'] = $details['columns'];
        $this->setTable($details, $table);        
    }
    
    public function dropUniqueKey($details)
    {
        $table = $this->getTable($details);
        unset($table['unique_keys'][$details['name']]);
        $this->setTable($details, $table);        
    }
    
    public function addIndex($details)
    {
        $table = $this->getTable($details);
        $table['indices'][$details['name']]['columns'] = $details['columns'];
        $this->setTable($details, $table);
    }
    
    public function dropIndex($details)
    {
        $table = $this->getTable($details);
        unset($table['indices'][$details['name']]);
        $this->setTable($details, $table);        
    }
    
    public function addForeignKey($details)
    {
        $table = $this->getTable($details);
        $table['foreign_keys'][$details['name']] = $details;
        $this->setTable($details, $table);
    }
    
    public function dropForeignKey($details)
    {
        $table = $this->getTable($details);
        unset($table['foreign_keys'][$details['name']]);
        $this->setTable($details, $table);
    }
        
    public static function wrap($description)
    {
        return new SchemaDescription($description);
    }

    public function toArray()
    {
        return $this->description;
    }    
}
