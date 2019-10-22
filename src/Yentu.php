<?php

/*
 * The MIT License
 *
 * Copyright 2015 James Ekow Abaka Ainooson.
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

namespace yentu;

use clearice\io\Io;
use yentu\manipulators\AbstractDatabaseManipulator;


/**
 * Utility class for yentu related functions.
 */
class Yentu
{

    /**
     * The current home path for yentu.
     * The home path represents the location of migrations and the configurations used for the yentu session.
     *
     * @var string
     */
    private $home;

    /**
     * An instance of the clearice Io class
     * @var Io
     */
    private $io;
    private $databaseManipulatorFactory;
    private $migrationVariables;
    private $otherMigrations;

    /**
     * Current version of yentu.
     * @var string
     */
    const VERSION = 'v0.3.0';

    public function __construct(Io $io, DatabaseManipulatorFactory $databaseManipulatorFactory, $migrationVariables = [], $otherMigrations = [])
    {
        $this->databaseManipulatorFactory = $databaseManipulatorFactory;
        $this->io = $io;
        $this->migrationVariables = $migrationVariables;
        $this->otherMigrations = $otherMigrations;
    }

    /**
     * Set the current home of yentu.
     * The home of yentu contains is a directory which contains the yentu 
     * configurations and migrations. Configurations are stored in the config
     * sub directory and the migrations are stored in the migrations sub
     * directory. 
     * @param string $home
     */
    public function setDefaultHome($home)
    {
        $this->home = $home;
    }

    /**
     * Returns a path relative to the current yentu home.
     * @param string $path
     * @return string
     */
    public function getPath($path)
    {
        return $this->home . "/$path";
    }

    /**
     * Announce a migration based on the command and the arguments called for
     * the migration.
     *
     * @param string $command The action being performed
     * @param string $itemType The type of item
     * @param array $arguments The arguments of the 
     */
    public function announce($command, $itemType, $arguments)
    {
        $this->io->output(
            "\n  - " . ucfirst("{$command}ing ") .
            preg_replace("/([a-z])([A-Z])/", "$1 $2", $itemType) . " " .
            $this->getEventDescription($command, Parameters::wrap($arguments)), Io::OUTPUT_LEVEL_2
        );
        $this->io->output(".");
    }

    /**
     * Convert the arguments of a migration event to a string description.
     * 
     * @param string $command
     * @param array $arguments
     * @return string
     */
    private function getEventDescription($command, $arguments)
    {
        $dir = '';
        $destination = '';
        $arguments = Parameters::wrap($arguments, ['name' => null]);

        if ($command == 'add') {
            $dir = 'to';
        } else if ($command == 'drop') {
            $dir = 'from';
        }

        if (isset($arguments['table']) && isset($arguments['schema'])) {
            $destination = "table " .
                ($arguments['schema'] != '' ? "{$arguments['schema']}." : '' ) .
                "{$arguments['table']}'";
        } elseif (isset($arguments['schema']) && !isset($arguments['table'])) {
            $destination = "schema '{$arguments['schema']}'";
        }

        if (is_string($arguments)) {
            return $arguments;
        }

        if (isset($arguments['column'])) {
            $item = $arguments['column'];
        } else {
            $item = $arguments['name'];
        }

        return "'$item' $dir $destination";
    }

    /**
     * Reverses a command which is reversible.
     * 
     * @param \yentu\Reversible $command
     */
    public function reverseCommand($command)
    {
        if ($command instanceof \yentu\Reversible) {
            $command->reverse();
        }
    }
    
    /**
     * Display the greeting for the CLI user interface.
     */
    public function greet()
    {
        $version = $this->getVersion();
        $welcome = <<<WELCOME
Yentu Database Migration Tool
Version $version


WELCOME;
        $this->io->output($welcome);
    }

    public function getVersion()
    {
        if (defined('PHING_BUILD_VERSION')) {
            return PHING_BUILD_VERSION;
        } else {
            $version = new \SebastianBergmann\Version(Yentu::VERSION, dirname(__DIR__));
            return $version->getVersion();
        }
    }

}
