<?php

namespace yentu\commands;


use clearice\io\Io;
use ntentan\config\Config;
use yentu\DatabaseManipulatorFactory;
use yentu\Yentu;

class Command
{
    protected $yentu;
    protected $manipulatorFactory;
    protected $io;

    public function __construct(Yentu $yentu, DatabaseManipulatorFactory $manipulatorFactory = null, Io $io = null)
    {
        $this->manipulatorFactory = $manipulatorFactory;
        $this->yentu = $yentu;
        $this->io = $io;
    }
}