<?php
namespace yentu\database;

class UniqueKey extends BasicKey
{ 
    #[\Override]
    protected function addKey($constraint) 
    {
        $this->getChangeLogger()->addUniqueKey($constraint);
    }

    #[\Override]
    protected function doesKeyExist($constraint) 
    {
        return $this->getChangeLogger()->doesUniqueKeyExist($constraint);
    }

    #[\Override]
    protected function dropKey($constraint) 
    {
        $this->getChangeLogger()->dropUniqueKey($constraint);
    }

    #[\Override]
    protected function getNamePostfix() 
    {
        return 'uk';
    }
}

