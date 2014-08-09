<?php
namespace yentu\database;

class Table extends DatabaseItem
{
    private $schema;
    private $name;
    private $driver;
    private $primaryKeyColumns;
    
    public function __construct($name, $driver, $schema = false) 
    {
        $this->name = $name;
        $this->driver = $driver;
        $this->schema = $schema;
        $driver->addTable(array(
            'name' => $name, 
            'schema' => $schema->getName()
            )
        );
        $this->setEncapsulated($schema);
    }
    
    public function column($name)
    {
        return new Column($name, $this);
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getSchema()
    {
        return $this->schema;
    }
    
    public function getDriver()
    {
        return $this->driver;
    }
    
    public function primaryKey()
    {
        $columns = func_get_args();
        $this->driver->addPrimaryKey(
            array(
                'table' => $this->name, 
                'schema' => $this->schema->getName(), 
                'columns' => $columns
            )
        );
        $this->primaryKeyColumns = $columns;
        return $this;
    }
    
    public function unique()
    {
        $columns = func_get_args();
        $this->driver->addUniqueConstraint(
            array(
                'table' => $this->name,
                'schema' => $this->schema->getName(),
                'columns' => $columns
            )
        );
        return $this;
    }
    
    public function autoIncrement()
    {
        if(count($this->primaryKeyColumns) > 1)
        {
            throw new \Exception("Cannot make an auto incementing composite key.");
        }
        $this->driver->makeAutoPrimaryKey(
            array(
                'table' => $this->name,
                'schema' => $this->schema->getName(),
                'column' => $this->primaryKeyColumns[0]
            )
        );
        return $this;
    }
}
