<?php

namespace yentu;

use clearice\io\Io;
use yentu\factories\DatabaseManipulatorFactory;


/**
 * Provides an interface for accessing migrations.
 *
 * @package yentu
 */
class Migrations
{
    /**
     * An instance of the database manipulator.
     * @var DatabaseManipulatorFactory
     */
    private $manipulatorFactory;
    private $config;
    private $io;

    public function __construct(Io $io, DatabaseManipulatorFactory $manipulatorFactory, array $config = [])
    {
        $this->manipulatorFactory = $manipulatorFactory;
        $this->config = $config;
        $this->io = $io;
    }

    public function getAllPaths()
    {
        return array_merge(
            array(
                array(
                    'home' => $this->getPath('migrations'),
                    'variables' => $this->config['variables']
                )
            ), $this->config['other_migrations']
        );
    }

    /**
     * Return an array of all migrations available.
     *
     * @param string $path
     * @return array
     */
    public function getMigrationFiles($path)
    {
        if (!file_exists($path))
            return [];
        $migrationFiles = scandir($path, 0);
        $migrations = array();
        foreach ($migrationFiles as $migration) {
            $details = $this->getMigrationDetails($migration);
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
     * @return array|bool
     */
    private function getMigrationDetails($migration)
    {
        if (preg_match("/^(?<timestamp>[0-9]{14})\_(?<migration>[a-z][a-z0-9\_]*)\.php$/", $migration, $details)) {
            $details['file'] = $migration;
        } else {
            $details = false;
        }
        return $details;
    }
    /**
     * Returns an array of all migrations, in all configured migrations
     * directories.
     * @return array
     */
    public function getAllMigrations()
    {
        $migrations = array();
        foreach ($this->getAllPaths() as $migration) {
            $migrations = $migrations + $this->getMigrationFiles($migration['home']);
        }
        return $migrations;
    }


    /**
     * Returns an array of all migrations that have been run on the database.
     * The information returned includes the timestamp, the name of the migration
     * and the default schema on which it was run.
     * @return array
     * @throws exceptions\DatabaseManipulatorException
     */
    public function getRunMirations()
    {
        $db = $this->manipulatorFactory->createManipulator();
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
     * Returns a path relative to the current yentu home.
     * @param string $path
     * @return string
     */
    public function getPath($path)
    {
        return ($this->config['home'] ?? './yentu') . DIRECTORY_SEPARATOR . $path;
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
            $this->getMigrationEventDescription($command, Parameters::wrap($arguments)), Io::OUTPUT_LEVEL_2
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
    private function getMigrationEventDescription($command, $arguments)
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
}