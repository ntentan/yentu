<?php
namespace yentu\database;

class PrimaryKey extends BasicKey
{ 
    protected function addKey($constraint) 
    {
        $this->getDriver()->addPrimaryKey($constraint);        
    }

    protected function doesKeyExist($constraint) 
    {
        return $this->getDriver()->doesPrimaryKeyExist($constraint);        
    }

    protected function dropKey($constraint) 
    {
        $this->getDriver()->dropPrimaryKey($constraint);        
    }

    protected function getNamePostfix() 
    {
        return 'pk';
    }

}
