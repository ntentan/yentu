<?php
namespace yentu\database;

use yentu\Parameters;

class Column extends DatabaseItem
{
    private $type;
    private $table;
    private $length;

    // Description items
    public $nulls;
    public $name;
    public $default;
    
    protected function buildDescription()
    {
        return array(
            'name' => $this->name,
            'type' => $this->type,
            'table' => $this->table->getName(),
            'schema' => $this->table->getSchema()->getName(),
            'nulls' => $this->nulls,
            'length' => $this->length,
            'default' => $this->default
        );
    }
    
    public function __construct($name, $table)
    {
        $this->table = $table;
        $this->name = $name;
        $column = Parameters::wrap($this->getDriver()->doesColumnExist(
                array(
                    'table' => $table->getName(),
                    'schema' => $table->getSchema()->getName(),
                    'name' => $name
                )
            ),
            ['default', 'length', 'nulls', 'type']
        );
        if($column === false) {
            $this->new = true;
        } else {
            $this->default = $column['default'];
            $this->length = $column['length'];
            $this->type = $column['type'];
            $this->nulls = $column['nulls'];
        }
    }
    
    public function type($type)
    {
        $this->type = $type;
        return $this;
    }
    
    public function nulls($nulls)
    {
       return $this->addChange('nulls', 'nulls', $nulls);
    }
    
    public function defaultValue($default)
    {
        return $this->addChange('default', 'default', $default);
    }

    public function commitNew() 
    {
        $this->getDriver()->addColumn($this->buildDescription());        
    }
    
    public function length($length)
    {
        $this->length = $length;
        return $this;
    }
    
    public function drop()
    {
        $this->getDriver()->dropColumn($this->buildDescription());
        return $this;
    }
}

