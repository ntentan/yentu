<?php

namespace yentu;

class DatabaseAssertor
{
    private $description;
    
    public function __construct($description)
    {        
        $this->description = $description;
    }
    
    public function doesSchemaExist($details)
    {
        return isset($this->description['schemata'][$details]);
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
            isset($this->description['tables'][$details['name']])  : 
            isset($this->description['schemata'][$details['schema']]['tables'][$details['name']]);
    }
    
    public function doesColumnExist($details)
    {
        $table = $this->getTableDetails($details['schema'], $details['table']);
        return isset($table['columns'][$details['name']]) ? 
            Parameters::wrap($table['columns'][$details['name']]) : false;
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
            return isset($table[$type][$details['name']]) ? $table[$type][$details['name']] : false;
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
    
    public function doesViewExist($details)
    {
        if(is_string($details))
        {
            $details = array(
                'schema' => false,
                'name' => $details
            );
        }
        
        // too complex 
        if($details['schema'] == false) {
            return isset($this->description['views'][$details['name']]) ? $this->description['views'][$details['name']]['definition'] : false ;
        } else {
            return (isset($this->description['schemata'][$details['schema']]['views'][$details['name']]) ?
                $this->description['schemata'][$details['schema']]['views'][$details['name']]['definition'] : false);
        }
    }
    
    private function getTableDetails($schema, $table)
    {
        if($schema) {
            return $this->description['schemata'][$schema]['tables'][$table];
        } else {
            return $this->description['tables'][$table];
        }
    }    
}
