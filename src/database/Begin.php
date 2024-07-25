<?php

namespace yentu\database;

use yentu\database\ItemType;

/**
 * Allows the
 */
class Begin extends DatabaseItem
{
    private Schema $defaultSchema;

    public function __construct(Schema $defaultSchema)
    {
        $this->defaultSchema = $defaultSchema;
    }

    public function table($name)
    {
        return $this->factory->create(ItemType::Table, $name, $this);
    }

    public function schema($name)
    {
        return $this->create('schema', $name, $this);
    }

    public function view($name)
    {
        return $this->create('view', $name, $this);
    }

    public function getName()
    {
        return $this->defaultSchema->getName();
    }

    #[\Override]
    protected function buildDescription()
    {
    }

    public function end()
    {
        //DatabaseItem::purge();
    }

    public function query($query, $bindData = array())
    {
        return $this->create('query', $query, $bindData);
    }

    #[\Override]
    public function init()
    {
        
    }
}
