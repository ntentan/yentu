<?php
namespace yentu\database;

class EncapsulatedStack
{
    private array $stack = [];
    
    public function push(DatabaseItem $encapsulated): void
    {
        $this->stack[] = $encapsulated;
    }
    
    public function pop(): DatabaseItem
    {
        return array_pop($this->stack);
    }
    
    public function top(): DatabaseItem
    {
        return end($this->stack);
    }
}

