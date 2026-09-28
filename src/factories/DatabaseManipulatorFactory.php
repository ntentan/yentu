<?php

namespace yentu\factories;

use clearice\io\Io;
use ntentan\atiaa\DriverFactoryInterface;
use yentu\exceptions\DatabaseManipulatorException;
use yentu\manipulators\AbstractDatabaseManipulator;
use yentu\Parameters;

/**
 * Description of DatabaseManipulatorFactory
 *
 * @author ekow
 */
class DatabaseManipulatorFactory
{
    private $driverFactory;
    private $io;
    
    public function __construct(DriverFactoryInterface $driverFactory, Io $io)
    {
        $this->driverFactory = $driverFactory;
        $this->io = $io;
    }
    
    public function createManipulator() : AbstractDatabaseManipulator
    {
        $config = Parameters::parseDsn($this->driverFactory->getConfig());
        $class = "\\yentu\\manipulators\\" . ucfirst($config['driver']);
        if(class_exists($class)) {
            return new $class($this->driverFactory, $this->io);
        } else {
            throw new DatabaseManipulatorException("Database manipulator class [$class] does not exist.");
        }
    }

    public function createManipulatorWithConfig($config) : AbstractDatabaseManipulator
    {
        $config = Parameters::parseDsn($config);
        $this->driverFactory->setConfig($config);
        return $this->createManipulator();
    }

}
