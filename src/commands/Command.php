<?php

namespace yentu\commands;

use clearice\io\Io;
use yentu\factories\DatabaseManipulatorFactory;
use yentu\Yentu;

abstract class Command
{
    protected $yentu;
    protected $manipulatorFactory;
    protected $io;
    protected $options;

    public function __construct(Yentu $yentu, DatabaseManipulatorFactory $manipulatorFactory = null, Io $io = null, array $options = null)
    {
        $this->manipulatorFactory = $manipulatorFactory;
        $this->yentu = $yentu;
        $this->io = $io;
        $this->options = $options;
    }

    abstract public function run();
}