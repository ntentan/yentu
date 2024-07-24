<?php

namespace yentu\factories;

use clearice\io\Io;
use yentu\exceptions\DatabaseManipulatorException;
use yentu\manipulators\AbstractDatabaseManipulator;
use ntentan\atiaa\DriverFactory;

/**
 * Description of DatabaseManipulatorFactory
 *
 * @author ekow
 */
class DatabaseManipulatorFactory
{
    private $driverFactory;
    private $io;
    
    public function __construct(DriverFactory $driverFactory, Io $io)
    {
        $this->driverFactory = $driverFactory;
        $this->io = $io;
    }
    
    public function createManipulator() : AbstractDatabaseManipulator
    {
        $config = $this->driverFactory->getConfig();
        $class = "\\yentu\\manipulators\\" . ucfirst($config['driver']);
        if(class_exists($class)) {
            return new $class($this->driverFactory, $this->io);
        } else {
            throw new DatabaseManipulatorException("Database manipulator class [$class] does not exist.");
        }
    }

    public function createManipulatorWithConfig($config) : AbstractDatabaseManipulator
    {
        $this->driverFactory->setConfig($config);
        return $this->createManipulator();
    }

}
