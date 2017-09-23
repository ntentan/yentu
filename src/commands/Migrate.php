<?php

/*
 * The MIT License
 *
 * Copyright 2014 James Ekow Abaka Ainooson
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

namespace yentu\commands;

use yentu\database\DatabaseItem;
use yentu\DatabaseManipulatorFactory;
use yentu\ChangeLogger;
use yentu\Yentu;
use yentu\database\ForeignKey;
use yentu\Reversible;
use ntentan\config\Config;
use clearice\ConsoleIO;

/**
 * The migrate command for the yentu database migration system. This class is
 * responsible for creating and updating items 
 */
class Migrate implements Reversible
{

    const FILTER_UNRUN = 'unrun';
    const FILTER_LAST_SESSION = 'lastSession';

    private $driver;
    private $dryDriver;
    private $defaultSchema = false;
    private $lastSession;
    private $currentPath;
    private $yentu;
    private $manipulator;
    private $config;
    private $io;

    public function __construct(Yentu $yentu, DatabaseManipulatorFactory $manipulatorFactory, Config $config, ConsoleIO $io)
    {
        $this->manipulator = $manipulatorFactory->createManipulator();
        $this->yentu = $yentu;
        $this->config = $config;
        $this->io = $io;
    }

    public function setupOptions($options, &$filter)
    {
        if (isset($options['no-foreign-keys'])) {
            $this->io->output("Ignoring all foreign key constraints ...\n");
            $this->driver->skip('ForeignKey');
        }

        if (isset($options['only-foreign-keys'])) {
            $this->io->output("Applying only foreign keys ...\n");
            $this->lastSession = $this->driver->getLastSession();
            $this->driver->allowOnly('ForeignKey');
            $filter = self::FILTER_LAST_SESSION;
        }

        if (isset($options['force-foreign-keys'])) {
            $this->io->output("Applying only foreign keys and skipping on errors ...\n");
            $this->lastSession = $this->driver->getLastSession();
            $this->driver->setSkipOnErrors($options['force-foreign-keys']);
            $this->driver->allowOnly('ForeignKey');
            $filter = self::FILTER_LAST_SESSION;
        }

        if (isset($options['default-ondelete-action'])) {
            ForeignKey::$defaultOnDelete = $options['default-ondelete-action'];
        }

        if (isset($options['default-onupdate-action'])) {
            ForeignKey::$defaultOnUpdate = $options['default-onupdate-action'];
        }

        $this->setDefaultSchema($options);
    }

    private function setDefaultSchema($options)
    {
        global $defaultSchema;
        if (isset($options['default-schema'])) {
            $this->driver->setDefaultSchema($options['default-schema']);
            $this->defaultSchema = $options['default-schema'];
            $defaultSchema = $this->defaultSchema;
        }
    }

    private function announceMigration($migrations, $path)
    {
        $size = count($migrations);
        $defaultSchema = null;
        if ($size > 0) {
            if (isset($path['default-schema'])) {
                $defaultSchema = $path['default-schema'];
            }
            $this->io->output("Running $size migration(s) from '{$path['home']}'");
            if ($defaultSchema != '') {
                $this->io->output(" with '$defaultSchema' as the default schema.\n");
            }
        } else {
            $this->io->output("No migrations to run from '{$path['home']}'\n");
        }
    }

    public function getBegin()
    {
        return new \yentu\database\Begin($this->defaultSchema);
    }

    private static function fillOptions(&$options)
    {
        if (!isset($options['dump-queries'])) {
            $options['dump-queries'] = false;
        }
        if (!isset($options['dry'])) {
            $options['dry'] = false;
        }
    }

    public function run($options = array())
    {
        global $migrateCommand;
        global $migrateVariables;

        self::fillOptions($options);

        $migrateCommand = $this;

        if ($options['dump-queries'] !== true) {
            $this->yentu->greet();
        }

        $this->driver = ChangeLogger::wrap($this->manipulator, $this->yentu, $this->io);
        $this->driver->setDumpQueriesOnly($options['dump-queries']);
        $this->driver->setDryRun($options['dry']);

        $totalOperations = 0;

        $filter = self::FILTER_UNRUN;
        $this->setupOptions($options, $filter);
        DatabaseItem::setDriver($this->driver);

        \yentu\Timer::start();
        $migrationPaths = $this->getMigrationPathsInfo();
        $migrationsToBeRun = [];
        foreach ($migrationPaths as $path) {
            $this->setDefaultSchema($path);
            $migrateVariables = $path['variables'] ?? [];
            $migrations = $this->filter($this->yentu->getMigrations($path['home']), $filter);
            $this->announceMigration($migrations, $path);
            $this->currentPath = $path;

            foreach ($migrations as $migration) {
                $this->countOperations("{$path['home']}/{$migration['file']}");
                $this->driver->setVersion($migration['timestamp']);
                $this->driver->setMigration($migration['migration']);
                $this->io->output("\nApplying '{$migration['migration']}' migration\n");
                require "{$path['home']}/{$migration['file']}";
                DatabaseItem::purge();
                $this->io->output("\n");
                $totalOperations += $this->driver->resetOperations();
            }
        }

        if ($this->driver->getChanges()) {
            $elapsed = \yentu\Timer::stop();
            $this->io->output("\nMigration took " . \yentu\Timer::pretty($elapsed) . "\n");
            $this->io->output($this->driver->getChanges() . " operations performed\n");
            $this->io->output($totalOperations - $this->driver->getChanges() . " operations skipped\n");
        }

        $this->driver->disconnect();
    }

    private function filter($migrations, $type = self::FILTER_UNRUN)
    {
        $filterMethod = "{$type}Filter";
        return $this->$filterMethod($migrations);
    }

    private function countOperations($migrationFile)
    {
        if ($this->dryDriver === null) {
            $this->dryDriver = clone $this->driver;
            $this->dryDriver->setDryRun(true);
        }
        $this->io->pushOutputLevel(ConsoleIO::OUTPUT_LEVEL_0);
        DatabaseItem::setDriver($this->dryDriver);
        require "$migrationFile";
        DatabaseItem::purge();
        DatabaseItem::setDriver($this->driver);
        $this->io->popOutputLevel();
        $this->driver->setExpectedOperations($this->dryDriver->resetOperations());
    }

    public function getCurrentPath()
    {
        return $this->currentPath;
    }

    private function unrunFilter($input)
    {
        $output = array();
        foreach ($input as $migration) {
            $run = $this->driver->query(
                "SELECT count(*) as number_run FROM yentu_history WHERE migration = ? and version = ? and default_schema = ?", 
                array($migration['migration'], $migration['timestamp'], (string) $this->defaultSchema)
            );

            if ($run[0]['number_run'] == 0) {
                $output[] = $migration;
            }
        }
        return $output;
    }

    private function lastSessionFilter($input)
    {
        $versions = $this->driver->getSessionVersions($this->lastSession);
        $output = array();
        foreach ($input as $migration) {
            if (array_search($migration['timestamp'], $versions) !== false) {
                $output[] = $migration;
            }
        }
        return $output;
    }
    
    private function getMigrationPathsInfo()
    {
        $variables = $this->config->get('variables', []);
        $otherMigrations = $this->config->get('other_migrations', []);

        return array_merge(
            array(
            array(
                'home' => $this->yentu->getPath('migrations'),
                'variables' => $variables
            )
            ), $otherMigrations
        );
    }    

    public function getChanges()
    {
        return $this->driver->getChanges();
    }

    public function reverse()
    {
        if ($this->driver === null) {
            return;
        }

        $this->io->output("Attempting to reverse all changes ... ");
        if ($this->getChanges() > 0) {
            $this->io->pushOutputLevel(0);
            $rollback = $this->yentu->getContainer()->resolve(\yentu\commands\Rollback::class);
            $rollback->run(array());
            $this->io->popOutputLevel();
        }
        $this->io->output("OK\n");
    }

}
