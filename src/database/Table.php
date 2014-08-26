<?php
namespace yentu\database;

class Table extends DatabaseItem
{
    private $schema;
    private $name;
    private $primaryKeyColumns;
    
    public function __construct($name,  $schema = false) 
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
        return DatabaseItem::create('column', $name, $this);
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
        return DatabaseItem::create('primary_key', func_get_args(), $this);
    }
    
    public function index()
    {
        return DatabaseItem::create('index', func_get_args(), $this);
    }
    
    public function unique()
    {
        return DatabaseItem::create('unique_key', func_get_args(), $this);
    }
        
    public function foreignKey()
    {
        return DatabaseItem::create('foreign_key', func_get_args(), $this);
    }

    public function commitNew() {
        
    }

}
