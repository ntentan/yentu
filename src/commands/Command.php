<?php

namespace yentu\commands;


use clearice\io\Io;
use ntentan\config\Config;
use yentu\DatabaseManipulatorFactory;
use yentu\Yentu;

class Command
{
    protected $config;
    protected $yentu;
    protected $manipulatorFactory;
    protected $io;

    public function __construct(Yentu $yentu, DatabaseManipulatorFactory $manipulatorFactory = null, Io $io = null, Config $config = null)
    {
        $this->manipulatorFactory = $manipulatorFactory;
        $this->yentu = $yentu;
        $this->config = $config;
        $this->io = $io;
    }
}