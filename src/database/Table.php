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
        $this->getDriver()->addTable(array(
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
    
    public function primaryKey()
    {
        $columns = func_get_args();
        $this->getDriver()->addPrimaryKey(
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
        $this->getDriver()->addUniqueConstraint(
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
        $this->getDriver()->makeAutoPrimaryKey(
            array(
                'table' => $this->name,
                'schema' => $this->schema->getName(),
                'column' => $this->primaryKeyColumns[0]
            )
        );
        return $this;
    }
    
    public function foreignKey()
    {
        return new ForeignKey(func_get_args(), $this);
    }
}
