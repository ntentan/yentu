<?php

namespace yentu;

use ntentan\config\Config;
use yentu\factories\DatabaseManipulatorFactory;


class Migrations
{
    private $manipulatorFactory;
    private $config;

    public function __construct(DatabaseManipulatorFactory $manipulatorFactory, Config $config)
    {
        $this->manipulatorFactory = $manipulatorFactory;
        $this->config = $config;
    }

    private function getMigrationPathsInfo()
    {
        $variables = $this->config->get('variables', []);
        $otherMigrations = $this->config->get('other_migrations', []);
        return array_merge(
            array(
                array(
                    'home' => 'yentu/migrations', //$this->yentu->getPath('migrations'),
                    'variables' => $variables
                )
            ), $otherMigrations
        );
    }

    /**
     * Return an array of all migrations available.
     *
     * @param string $path
     * @return array
     */
    public function getMigrations($path)
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
     * @return array
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
        foreach ($this->getMigrationPathsInfo() as $migration) {
            $migrations = $migrations + $this->getMigrations($migration['home']);
        }
        return $migrations;
    }


    /**
     * Returns an array of all migrations that have been run on the database.
     * The information returned includes the timestamp, the name of the migration
     * and the default schema on which it was run.
     * @return array
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
}