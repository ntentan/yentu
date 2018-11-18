<?php

namespace yentu\factories;


use clearice\io\Io;
use yentu\Yentu;

class CommandFactory
{
    private $io;
    private $yentu;
    private $manipulatorFactory;

    public function __construct(Io $io, DatabaseManipulatorFactory $manipulatorFactory, Yentu $yentu)
    {
        $this->io = $io;
        $this->manipulatorFactory = $manipulatorFactory;
        $this->yentu = $yentu;
    }

    public function createCommand($command, $options = [])
    {
        $class = "yentu\\commands\\" . ucfirst($command);
        return new $class($this->yentu, $this->manipulatorFactory, $this->io, $options);
    }
}
