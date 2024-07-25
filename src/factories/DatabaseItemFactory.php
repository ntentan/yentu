<?php
namespace yentu\factories;

use yentu\database\DatabaseItem;
use yentu\database\ItemType;
use yentu\database\EncapsulatedStack;
use yentu\ChangeLogger;


class DatabaseItemFactory
{
    private string $home;
    private EncapsulatedStack $stack;
    private ChangeLogger $driver;
    
    public function __construct(EncapsulatedStack $stack, array $arguments)
    {
        $this->home = $arguments['home'] ?? './yentu';
        $this->stack = $stack;
    }
    
    public function setDriver(ChangeLogger $driver): void
    {
        $this->driver = $driver;
    }
    
    public function create(ItemType $itemType, mixed ... $args): DatabaseItem
    {
        $class = new \ReflectionClass($itemType->value);
        $item = $class->newInstanceArgs($args);
        $item->setDriver($this->driver);    
        $item->init();              
        $item->setFactory($this);
        $item->setStack($this->stack);
        $item->setHome($this->home);
        $this->stack->push($item);          
        return $item;
    }   
    
    public function getEncapsulatedStack(): EncapsulatedStack
    {
        return $this->stack;
    }
}
