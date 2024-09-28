<?php
namespace yentu\database;

abstract class BasicKey extends DatabaseItem implements Commitable, Changeable, Initializable
{
    protected $columns;
    protected $table;
    protected $name;
    
    public function __construct($columns, $table)
    {
        $this->columns = $columns;
        $this->table = $table;
    }
    
    #[\Override]
    public function initialize(): void
    {
        $keyName = $this->doesKeyExist(array(
            'table' => $this->table->getName(),
            'schema' => $this->table->getSchema()->getName(),
            'columns' => $this->columns)
        );
        if($keyName === false)
        {
            $this->new = true;
            $this->name = $this->table->getName() . '_' . implode('_', $this->columns) . '_' . $this->getNamePostfix();
        }
        else
        {
            $this->name = $keyName;
        }        
    }
    
    abstract protected function doesKeyExist($constraint);
    abstract protected function addKey($constraint);
    abstract protected function dropKey($constraint);
    abstract protected function getNamePostfix();
    
    #[\Override]
    public function buildDescription()
    {
        return array(
            'table' => $this->table->getName(), 
            'schema' => $this->table->getSchema()->getName(), 
            'columns' => $this->columns,
            'name' => $this->name
        );
    }

    #[\Override]
    public function commitNew() 
    {
        $this->addKey($this->buildDescription());
    }
    
    public function drop()
    {
        $this->dropKey($this->getKeyDescription());
        return $this;
    }
    
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }    
}

