<?php
namespace yentu\database;

use yentu\database\DatabaseItem;


class View extends DatabaseItem implements Changeable
{
    private $name;
    private $schema;
    
    public $definition;
    
    public function __construct($name, $schema) 
    {
        $this->name = $name;
        $this->schema = $schema;
    }
    
    #[\Override]
    public function init()
    {
        $this->definition = $this->getDriver()->doesViewExist($this->buildDescription());
        if($this->definition === false)
        {
            $this->new = true;
        }        
    }
    
    public function drop()
    {
        $this->getDriver()->dropView($this->buildDescription());
        return $this;
    }
    
    public function definition($definition)
    {
        $this->addChange('definition', 'definition', $definition);
        
        if($this->isNew())
        {
            $this->getDriver()->addView($this->buildDescription());
        }
        return $this;
    }
    
    public function view($name)
    {
        DatabaseItem::purge();
        return $this->create('view', $name, $this->schema);
    }
    
    public function table($name)
    {
        DatabaseItem::purge();
        return $this->create('table', $name, $this->schema);
    }
    
    #[\Override]
    protected function buildDescription() {
        return array(
            'name' => $this->name,
            'schema' => $this->schema->getName(),
            'definition' => $this->definition
        );
    }    
}
