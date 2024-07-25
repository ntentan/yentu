<?php

namespace yentu\database;

use yentu\exceptions\SyntaxErrorException;
use yentu\factories\DatabaseItemFactory;
use yentu\ChangeLogger;


abstract class DatabaseItem
{    
    private ?DatabaseItem $encapsulated = null;
    private ChangeLogger $driver;
    protected $new = false;
    private $changes = array();
    protected $home;
    protected DatabaseItemFactory $factory;
    private EncapsulatedStack $stack;   
    
    public function setFactory(DatabaseItemFactory $factory)
    {
        $this->factory = $factory;
    }

    protected function addChange($property, $attribute, $value)
    {
        if (!$this->isNew()) {
            $currentDescription = $this->buildDescription();
            $newDescription = $currentDescription;
            $newDescription[$attribute] = $value;
            $class = new \ReflectionClass($this);
            $name = $class->getShortName();

            $this->changes[] = \yentu\Parameters::wrap(array(
                'method' => "change{$name}" . str_replace('_', '', $attribute),
                'args' => array(
                    'from' => $currentDescription,
                    'to' => $newDescription
                )
            ));
        }

        (new \ReflectionProperty($this, $property))->setValue($this, $value);
        return $this;
    }

    public function isNew()
    {
        return $this->new;
    }

    protected function getDriver()
    {
        return $this->driver;
    }

    public function setDriver($driver)
    {
        $this->driver = $driver;
    }
    
    public function setStack(EncapsulatedStack $stack): void
    {
        $this->stack = $stack;
        if ($this->stack->hasItems()) {
            $this->encapsulated = $this->stack->top();            
        }
    }

    public function __call($method, $arguments)
    {
        if (!is_object($this->encapsulated)) {
            throw new SyntaxErrorException("Failed to call method '{$method}'", $this->home ?? '');
        } else if (method_exists($this->encapsulated, $method)) {
            $method = new \ReflectionMethod($this->encapsulated, $method);
            $this->commit();
            $this->stack->pop();
            return $method->invokeArgs($this->encapsulated, $arguments);
        } else {
            $this->commit();
            $this->stack->pop();
            return $this->encapsulated->__call($method, $arguments);
        }
    }

    public function commit()
    {
        if ($this instanceof Commitable && $this->isNew()) {
            $this->commitNew();
            $this->new = false;
        }

        foreach ($this->changes as $change) {
            $this->driver->{$change['method']}($change['args']);
        }

        return $this;
    }

    public function rename($newName)
    {
        return $this->addChange('name', 'name', $newName);
    }
    
    public function setHome(string $home): void
    {
        $this->home = $home;
    }
        
    abstract public function init();
//    abstract public function commitNew();
    abstract protected function buildDescription();
}
