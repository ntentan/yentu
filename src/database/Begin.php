<?php

namespace yentu\database;

/**
 * Allows the
 *
 * @package yentu\database
 */
class Begin extends DatabaseItem
{
    private Schema $defaultSchema;

    public function __construct($defaultSchema)
    {
        $this->defaultSchema = new Schema($defaultSchema);
    }

    public function table($name)
    {
        return $this->create('table', $name, $this);
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

    protected function buildDescription()
    {
    }

    public function commitNew()
    {
    }

    public function end()
    {
        DatabaseItem::purge();
    }

    public function query($query, $bindData = array())
    {
        return $this->create('query', $query, $bindData);
    }
}
