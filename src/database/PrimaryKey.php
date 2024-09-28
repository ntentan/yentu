<?php
namespace yentu\database;

class PrimaryKey extends BasicKey
{ 
    #[\Override]
    protected function addKey($constraint) 
    {
        $this->getChangeLogger()->addPrimaryKey($constraint);
    }

    #[\Override]
    protected function doesKeyExist($constraint) 
    {
        return $this->getChangeLogger()->doesPrimaryKeyExist($constraint);
    }

    #[\Override]
    protected function dropKey($constraint) 
    {
        $this->getChangeLogger()->dropPrimaryKey($constraint);
    }

    #[\Override]
    protected function getNamePostfix() 
    {
        return 'pk';
    }
    
    public function autoIncrement()
    {
        if(count($this->columns) > 1)
        {
            throw new \Exception("Cannot make an auto incementing composite key.");
        }
        
        $this->getChangeLogger()->addAutoPrimaryKey(
            \yentu\Parameters::wrap(array(
                    'table' => $this->table->getName(),
                    'schema' => $this->table->getSchema()->getName(),
                    'column' => $this->columns[0]
                )
            )
        );
        return $this;
    }
}
