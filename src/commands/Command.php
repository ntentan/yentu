<?php

namespace yentu\commands;

use yentu\Reversible;

abstract class Command
{
    protected $options;

    abstract public function run();

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function reverse()
    {
        if ($this instanceof Reversible) {
            $this->reverseActions();
        }
    }
}
