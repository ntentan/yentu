<?php

namespace yentu\commands;

abstract class Command
{
    protected array $options;

    abstract public function run();

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function reverse(): void
    {
        if ($this instanceof Reversible) {
            $this->reverseActions();
        }
    }
}
