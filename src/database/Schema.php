<?php

namespace yentu\database;

class Schema extends DatabaseItem
{
    private $name;
    
    public function __construct($name)
    {
        $this->name = $name;
        if(!$this->getDriver()->doesSchemaExist($name))
        {        
            $this->getDriver()->addSchema($name);
        }
    }
    
    public function table($name)
    {
        return $this->create('table', $name, $this);
    }
    
    public function view($name)
    {
        return $this->create('view', $name, $this);
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function commitNew() {
        
    }

    protected function buildDescription() {
        return array(
            'name' => $this->name
        );
    }

}

