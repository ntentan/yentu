<?php
namespace yentu\database;

class View extends \yentu\database\DatabaseItem
{
    private $name;
    private $schema;
    private $definition;
    
    public function __construct($name, $schema) 
    {
        $this->name = $name;
        $this->schema = $schema;
        $this->definition = $this->getDriver()->doesViewExist($this->buildDescription());
        if($this->definition === false)
        {
            $this->new = true;
        }
    }
    
    public function drop()
    {
        $this->getDriver()->dropView($this->buildDescription());
    }
    
    public function commitNew() 
    {
        $this->getDriver()->addView($this->buildDescription());
    }
    
    public function definition($definition)
    {
        if(!$this->isNew())
        {
            $this->addChange('definition', $definition);
        }
        $this->definition = $definition;
        return $this;
    }
    
    public function view($name)
    {
        DatabaseItem::purge();
        return $this->create('view', $name, $this->schema);
    }
    
    protected function buildDescription() {
        return array(
            'name' => $this->name,
            'schema' => $this->schema->getName(),
            'definition' => $this->definition
        );
    }    
}
