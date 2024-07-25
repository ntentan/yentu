<?php

namespace yentu\database;


class Schema extends DatabaseItem
{
    private $name;
    private $isReference;
    
    public function __construct($name)
    {
        $this->name = $name;
    }
    
    #[\Override]
    public function init()
    {
        if(!$this->getDriver()->doesSchemaExist($this->name)) {        
            $this->getDriver()->addSchema($this->name);
        }   
    }
    
    public function isReference()
    {
        return $this->isReference;
    }
    
    public function setIsReference($isReference)
    {
        $this->isReference = $isReference;
    }
    
    public function table($name)
    {
        if($this->isReference) {
            $table = new Table($name, $this);
        } else {
            $table = $this->create('table', $name, $this);
        }
        $table->setIsReference($this->isReference);
        return $table;
    }
    
    public function view($name)
    {
        return $this->create('view', $name, $this);
    }
    
    public function getName()
    {
        return $this->name;
    }

    #[\Override]
    protected function buildDescription() {
        return array(
            'name' => $this->name
        );
    }
}

