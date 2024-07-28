<?php
namespace yentu\database;

use yentu\Parameters;

class Column extends DatabaseItem implements Commitable, Changeable
{
    private $type;
    private $table;
    private $length;

    // Description items
    private $nulls;
    private $name;
    private $default;
    
    #[\Override]
    public function buildDescription()
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
    }
    
    #[\Override]
    public function init()
    {
        $column = Parameters::wrap($this->getDriver()->doesColumnExist(
                array(
                    'table' => $this->table->getName(),
                    'schema' => $this->table->getSchema()->getName(),
                    'name' => $this->name
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
    
    public function required(bool $required)
    {
        return $this->addChange('nulls', 'nulls', !$required);
    }
    
    public function defaultValue($default)
    {
        return $this->addChange('default', 'default', $default);
    }

    #[\Override]
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

