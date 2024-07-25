<?php
namespace yentu\database;

class UniqueKey extends BasicKey
{ 
    #[\Override]
    protected function addKey($constraint) 
    {
        $this->getDriver()->addUniqueKey($constraint);        
    }

    #[\Override]
    protected function doesKeyExist($constraint) 
    {
        return $this->getDriver()->doesUniqueKeyExist($constraint);        
    }

    #[\Override]
    protected function dropKey($constraint) 
    {
        $this->getDriver()->dropUniqueKey($constraint);        
    }

    #[\Override]
    protected function getNamePostfix() 
    {
        return 'uk';
    }
}

