<?php

namespace yentu\database;

use yentu\database\ItemType;


class Begin extends DatabaseItem
{
    private Schema $defaultSchema;
    private EncapsulatedStack $encapsulatedStack;

    public function __construct(Schema $defaultSchema, EncapsulatedStack $encapsulatedStack)
    {
        $this->defaultSchema = $defaultSchema;
        $this->encapsulatedStack = $encapsulatedStack;
    }

    public function table(string $name): Table
    {
        return $this->factory->create(ItemType::Table, $name, $this);
    }

    public function schema(string $name): Schema
    {
        return $this->create('schema', $name, $this);
    }

    public function view(string $name): View
    {
        return $this->create('view', $name, $this);
    }

    public function getName(): string
    {
        return $this->defaultSchema->getName();
    }

    public function end(): void
    {
        $this->encapsulatedStack->purge();
    }

    public function query(string $query, $bindData = array()): Query
    {
        return $this->create('query', $query, $bindData);
    }

    #[\Override]
    public function init()
    {
        
    }
}
