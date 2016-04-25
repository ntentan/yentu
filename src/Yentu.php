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

use clearice\ClearIce;
use ntentan\config\Config;

/**
 * Utility class for yentu related functions.
 */
class Yentu
{

    /**
     * The current home for yentu.
     * @see Yentu::setDefaultHome()
     * @var string
     */
    private static $home = './yentu';

    /**
     * Current version of yentu.
     * @var string
     */
    const VERSION = '0.1.0';

    /**
     * Set the current home of yentu.
     * The home of yentu contains is a directory which contains the yentu 
     * configurations and migrations. Configurations are stored in the config
     * sub directory and the migrations are stored in the migrations sub
     * directory. 
     * @param string $home
     */
    public static function setDefaultHome($home)
    {
        self::$home = $home;
    }

    /**
     * Returns a path relative to the current yentu home.
     * @param string $path
     * @return string
     */
    public static function getPath($path)
    {
        return self::$home . "/$path";
    }

    /**
     * Returns information about all migration paths.
     * Yentu can be configured to use multiple migration homes, for multiple
     * database configurations. This makes it possible to use a single command
     * to run multiple migrations accross multiple databases.
     * @return array
     */
    public static function getMigrationPathsInfo()
    {
        $variables = Config::get('default.variables', []);
        $otherMigrations = Config::get('default.other_migrations', []);
        
        return array_merge(
            array(
                array(
                    'home' => Yentu::getPath('migrations'),
                    'variables' => $variables
                )
            ), 
            $otherMigrations
        );
    }

    /**
     * Returns an array of all migrations that have been run on the database.
     * The information returned includes the timestamp, the name of the migration
     * and the default schema on which it was run.
     * @return array
     */
    public static function getRunMirations()
    {
        $db = DatabaseManipulator::create();
        $runMigrations = $db->query("SELECT DISTINCT version, migration, default_schema FROM yentu_history ORDER BY version");
        $migrations = array();
        foreach ($runMigrations as $migration) {
            $migrations[$migration['version']] = array(
                'timestamp' => $migration['version'],
                'migration' => $migration['migration'],
                'default_schema' => $migration['default_schema']
            );
        }

        return $migrations;
    }

    /**
     * Returns an array of all migrations, in all configured migrations 
     * directories.
     * @return array
     */
    public static function getAllMigrations()
    {
        $migrations = array();
        foreach (self::getMigrationPathsInfo() as $migration) {
            $migrations = $migrations + self::getMigrations($migration['home']);
        }
        return $migrations;
    }

    /**
     * Return an array of all migrations available.
     * 
     * @param string $path
     * @return array
     */
    public static function getMigrations($path)
    {
        if(!file_exists($path)) return [];
        $migrationFiles = scandir($path, 0);
        $migrations = array();
        foreach ($migrationFiles as $migration) {
            $details = self::getMigrationDetails($migration);
            if ($details === false)
                continue;
            $migrations[$details['timestamp']] = $details;
            unset($migrations[$details['timestamp']][0]);
            unset($migrations[$details['timestamp']][1]);
            unset($migrations[$details['timestamp']][2]);
        }

        return $migrations;
    }

    /**
     * Return the details of a migration extracted from the file name.
     * This method uses a regular expression to extract the timestamp and
     * migration name from the migration script.
     * 
     * @param string $migration
     * @return array
     */
    private static function getMigrationDetails($migration)
    {
        if (preg_match("/^(?<timestamp>[0-9]{14})\_(?<migration>[a-z][a-z0-9\_]*)\.php$/", $migration, $details)) {
            $details['file'] = $migration;
        } else {
            $details = false;
        }
        return $details;
    }

    /**
     * Announce a migration based on the command and the arguments called for
     * the migration.
     * 
     * @param string $command The action being performed
     * @param string $itemType The type of item
     * @param array $arguments The arguments of the 
     */
    public static function announce($command, $itemType, $arguments)
    {
        ClearIce::output(
            "\n  - " . ucfirst("{$command}ing ") .
            preg_replace("/([a-z])([A-Z])/", "$1 $2", $itemType) . " " .
            self::getDetails($command, Parameters::wrap($arguments)), ClearIce::OUTPUT_LEVEL_2
        );
        ClearIce::output(".");
    }

    /**
     * Convert the arguments of a migration event to a string description.
     * 
     * @param string $command
     * @param array $arguments
     * @return string
     */
    private static function getDetails($command, $arguments)
    {
        $dir = '';
        $destination = '';
        $arguments = Parameters::wrap(
            $arguments,
            ['name' => null]
        );
        
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

        if(is_string($arguments)) {
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
    public static function reverseCommand($command)
    {
        if ($command instanceof \yentu\Reversible) {
            $command->reverse();
        }
    }

    /**
     * Display the greeting for the CLI user interface.
     */
    public static function greet()
    {
        $version = Yentu::getVersion();
        $welcome = <<<WELCOME
Yentu Database Migration Tool
Version $version


WELCOME;
        ClearIce::output($welcome);
    }

    public static function getVersion()
    {
        if (defined('PHING_BUILD_VERSION')) {
            return PHING_BUILD_VERSION;
        } else {
            $version = new \SebastianBergmann\Version(Yentu::VERSION, dirname(__DIR__));
            return $version->getVersion();
        }
    }

}
