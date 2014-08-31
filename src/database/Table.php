<?php
namespace yentu\database;

class Table extends DatabaseItem
{
    private $schema;
    private $name;
    private $primaryKeyColumns;
    
    public function __construct($name,  $schema) 
    {
        $this->name = $name;
        $this->schema = $schema;
        $tableDescription = array(
            'name' => $name, 
            'schema' => $schema === false ? false :$schema->getName()
        );
        
        if(!$this->getDriver()->doesTableExist($tableDescription))
        {
            $this->getDriver()->addTable($tableDescription);
            $this->new = true;
        }
    }
    
    public function column($name)
    {
        return $this->create('column', $name, $this);
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getSchema()
    {
        return $this->schema;
    }
    
    public function primaryKey()
    {
        $this->primaryKeyColumns = func_get_args();
        return $this->create('primary_key', func_get_args(), $this);
    }
    
    public function index()
    {
        return $this->create('index', func_get_args(), $this);
    }
    
    public function unique()
    {
        return $this->create('unique_key', func_get_args(), $this);
    }
        
    public function foreignKey()
    {
        return $this->create('foreign_key', func_get_args(), $this);
    }
    
    public function table($name)
    {
        return $this->create('table', $name, $this->schema);
    }    

    public function commitNew() {
        
    }

    protected function buildDescription() {
        return array(
            'name' => $this->name,
            'schema' => $this->schema->getName()
        );
    }
}
