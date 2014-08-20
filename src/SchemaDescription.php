<?php
namespace yentu;

class SchemaDescription implements \ArrayAccess
{
    
    private $description = array(
        'schemata' => array(),
        'tables' => array()
    );
    
    private function __construct($description)
    {
        $this->description = $description;
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
            'columns' => array(),
            'primary_key' => array(),
            'unique_keys' => array(),
            'foreign_keys' => array(),
            'indices' => array()
        );
        
        if($details['schema'] != '')
        {
            $this->description['schemata'][$details['schema']]['tables'][$details['name']] = $table;
        }
        else
        {
            $this->description['tables'][$details['name']] = $table;
        }
    }
    
    public function dropTable($details)
    {
        if($details['schema'] != '')
        {
            unset($this->description['schemata'][$details['schema']]['tables'][$details['name']]);
        }
        else
        {
            unset($this->description['tables'][$details['name']]);
        }        
    }
    
    private function getTable($details)
    {
        if($details['schema'] == '')
        {
            return $this->description['tables'][$details['table']];
        }
        else
        {
            return $this->description['schemata'][$details['schema']]['tables'][$details['table']];
        }
    }
    
    private function setTable($details, $table)
    {
        if($details['schema'] == '')
        {
            $this->description['tables'][$details['table']] = $table;
        }
        else
        {
            $this->description['schemata'][$details['schema']]['tables'][$details['table']] = $table;
        }
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
    
    public function addPrimaryKey($details)
    {
        $table = $this->getTable($details);
        $table['primary_key'] = array(
            $details['name'] => $details['columns']
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
        $table['unique_keys'][$details['name']] = $details['columns'];
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
        $table['indices'][$details['name']] = $details['columns'];
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
        $table['foreign_keys'][$details['name']] = $details['columns'];
        $this->setTable($details, $table);
    }
    
    public function dropForeignKey($details)
    {
        $table = $this->getTable($details);
        unset($table['foreign_keys'][$details['name']]);
        $this->setTable($details, $table);
    }
    
    public function changeColumnNulls($details)
    {
        $table = $this->getTable($details);
        $table['columns'][$details['to']['name']]['nulls'] = $details['to']['nulls'];
        $this->setTable($details, $table);
    }
    
    public static function wrap($description)
    {
        return new SchemaDescription($description);
    }
}
