<?php

namespace yentu\database;

use yentu\database\ItemType;


class Begin extends DatabaseItem
{
    private Schema $defaultSchema;
    private EncapsulatedStack $encapsulatedStack;

    public function __construct(Schema $defaultSchema)
    {
        $this->defaultSchema = $defaultSchema;
//        $this->encapsulatedStack = $encapsulatedStack;
    }

    public function table(string $name): DatabaseItem
    {
        return $this->factory->create(ItemType::Table, $name, $this);
    }

    public function schema(string $name): DatabaseItem
    {
        return $this->factory->create(ItemType::Schema, $name, $this);
    }

    public function view(string $name): DatabaseItem
    {
        return $this->factory->create(ItemType::View, $name, $this);
    }

    public function getName(): string
    {
        return $this->defaultSchema->getName();
    }

    public function end(): void
    {
        $this->getStack()->purge();
    }

    public function query(string $query, $bindData = array()): DatabaseItem
    {
        return $this->factory->create(ItemType::Query, $query, $bindData);
    }
}
