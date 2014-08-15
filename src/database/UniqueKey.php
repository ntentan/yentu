<?php
namespace yentu\database;

class UniqueKey extends BasicKey
{ 
    protected function addKey($constraint) 
    {
        $this->getDriver()->addUniqueKey($constraint);        
    }

    protected function doesKeyExist($constraint) 
    {
        return $this->getDriver()->doesUniqueKeyExist($constraint);        
    }

    protected function dropKey($constraint) 
    {
        $this->getDriver()->dropUniqueKey($constraint);        
    }

    protected function getNamePostfix() 
    {
        return 'uk';
    }
}

