<?php
namespace yentu\database;


class Index extends \yentu\database\DatabaseItem
{
    private $name;
    private $unique = false;
    private $columns;
    private $table;
    
    public function __construct($columns, $table) 
    {
        $this->columns = $columns;
        $this->table = $table;
        $name = $this->getDriver()->doesIndexExist(array(
            'schema' => $table->getSchema()->getName(),
            'table' => $table->getName(),
            'columns' => $columns
        ));
        if($name === false)
        {
            $this->new = true;
        }
        else
        {
            $this->name = $name;
        }
    }
    
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function unique($unique = true)
    {
        $this->unique = $unique;
        return $this;
    }
    
    public function commitNew() 
    {
        if($this->name == '')
        {
            $this->name = substr($this->table->getName() . '_' . implode('_', $this->columns), 0, 60) . '_idx';
        }        
        $this->getDriver()->addIndex($this->buildDescription());
    }

    protected function buildDescription() 
    {
        return array(
            'table' => $this->table->getName(),
            'schema' => $this->table->getSchema()->getName(),
            'columns' => $this->columns,
            'name' => $this->name,
            'unique' => $this->unique
        );
    }
}
