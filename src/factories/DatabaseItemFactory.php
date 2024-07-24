<?php
namespace yentu\factories;

use yentu\database\DatabaseItem;
use yentu\database\ItemType;
use yentu\database\EncapsulatedStack;


class DatabaseItemFactory
{
    private string $home;
    private EncapsulatedStack $stack;
    
    public function __construct(EncapsulatedStack $stack, string $home)
    {
        $this->home = $home;
        $this->stack = $stack;
    }
    
    public function create(ItemType $itemType, mixed ... $args): DatabaseItem
    {
        $class = new \ReflectionClass($itemType->value);
        $item = $class->newInstanceArgs($args);
        $item->setEncapsulated($this->stack->top());
        $item->setHome($this->home);
        $this->stack->push($item);          
        return $item;
    }        
}
