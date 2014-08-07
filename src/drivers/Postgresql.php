<?php
namespace yentu\drivers;

class Postgresql extends Pdo
{
    public function createTable() 
    {
        
    }
    
    private function buildTableName($name, $schema)
    {
        return ($schema === false ? '' : "\"$schema\".") . "\"$name\"";
    }

    protected function getDriverName() 
    {
        return 'pgsql';
    }

    public function addSchema($name) 
    {
        $this->query(sprintf('CREATE SCHEMA IF NOT EXISTS "%s"', $name));
    }
    
    public function dropSchema($name) 
    {
        //$this->query()
    }

    public function addTable($name, $schema) 
    {
        $this->query(sprintf('CREATE TABLE IF NOT EXISTS %s ()',  $this->buildTableName($name, $schema)));        
    }

    public function dropTable($name, $schema) 
    {
        
    }

    public function describe() 
    {
        $descriptor = new \yentu\descriptors\Postgresql($this);
        return $descriptor->describe();
    }
}
