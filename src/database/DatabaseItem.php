<?php

namespace yentu\database;

use yentu\exceptions\SyntaxErrorException;
use yentu\factories\DatabaseItemFactory;
use yentu\ChangeLogger;


class DatabaseItem
{    
    private ?DatabaseItem $encapsulated = null;
    private ChangeLogger $changeLogger;
    protected bool $new = false;
    private array $changes = [];
    protected string $home;
    protected DatabaseItemFactory $factory;
    private EncapsulatedStack $stack;   
    
    public function setFactory(DatabaseItemFactory $factory): void
    {
        $this->factory = $factory;
    }

    protected function addChange($property, $attribute, $value): DatabaseItem
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

    public function isNew(): bool
    {
        return $this->new;
    }

    protected function getChangeLogger(): ChangeLogger
    {
        return $this->changeLogger;
    }

    public function setChangeLogger($changeLogger): void
    {
        $this->changeLogger = $changeLogger;
    }
    
    public function setStack(EncapsulatedStack $stack): void
    {
        $this->stack = $stack;
        if ($this->stack->hasItems()) {
            $this->encapsulated = $this->stack->top();            
        }
    }

    public function getStack(): EncapsulatedStack
    {
        return $this->stack;
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

    public function commit(): DatabaseItem
    {
        if ($this instanceof Commitable && $this->isNew()) {
            $this->commitNew();
            $this->new = false;
        }

        foreach ($this->changes as $change) {
            $this->changeLogger->{$change['method']}($change['args']);
        }

        return $this;
    }

    public function rename($newName): DatabaseItem
    {
        return $this->addChange('name', 'name', $newName);
    }
    
    public function setHome(string $home): void
    {
        $this->home = $home;
    }

    public function init(): void
    {
        if ($this instanceof Initializable) {
            $this->initialize();
        }
    }
}
