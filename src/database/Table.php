<?php
namespace yentu\database;

class Table extends DatabaseItem
{
    /**
     *
     * @var Schema
     */
    private $schema;
    private $primaryKeyColumns;
    private $isReference;
    
    // Description
    public $name;
    
    public function __construct($name,  $schema) 
    {
        $this->name = $name;
        $this->schema = $schema;
        
        if(!$this->getDriver()->doesTableExist($this->buildDescription()))
        {
            $this->getDriver()->addTable($this->buildDescription());
            $this->new = true;
        }
    }
    
    public function setIsReference($isReference)
    {
        $this->isReference = $isReference;
    }
    
    public function isReference()
    {
        return $this->isReference;
    }
    
    public function column($name)
    {
        return $this->create('column', $name, $this);
    }
    
    public function insert()
    {
        
    }
    
    public function drop()
    {
        $table = $this->getDriver()->getDescription()->getTable(
            array(
                'table' => $this->name,
                'schema' => $this->schema->getName()
            )
        );
        $this->getDriver()->dropTable($table);
        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * 
     * @return Schema
     */
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
    
    public function view($name)
    {
        return $this->create('view', $name, $this->schema);
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
