<?php

/*
 * The MIT License
 *
 * Copyright 2017 ekow.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace yentu\factories;

use clearice\io\Io;
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
        return new $class($this->driverFactory, $this->io);
    }

    public function createManipulatorWithConfig($config) : AbstractDatabaseManipulator
    {
        $this->driverFactory->setConfig($config);
        return $this->createManipulator();
    }

}
