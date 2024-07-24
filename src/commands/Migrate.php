<?php

namespace yentu\commands;

use clearice\io\Io;
use yentu\database\Begin;
use yentu\database\DatabaseItem;
use yentu\ChangeLogger;
use yentu\database\ForeignKey;
use yentu\factories\DatabaseManipulatorFactory;
use yentu\Migrations;
use yentu\Yentu;


/**
 * The migrate class runs the command that creates database items.
 */
class Migrate extends Command implements Reversible
{

    const FILTER_UNRUN = 'unrun';
    const FILTER_LAST_SESSION = 'lastSession';

    private $driver;
    private $dryDriver;
    private $lastSession;
    private $currentPath;
    private $rollbackCommand;
    private $manipulatorFactory;
    private $migrations;
    private $io;

    public function __construct(Migrations $migrations, DatabaseManipulatorFactory $manipulatorFactory, Io $io)
    {
        $this->manipulatorFactory = $manipulatorFactory;
        $this->migrations = $migrations;
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
    }

    private function announceMigration($migrations, $path)
    {
        $size = count($migrations);
        if ($size > 0) {
            $this->io->output("Running $size migration(s) from '{$path['home']}'");
        } else {
            $this->io->output("No migrations to run from '{$path['home']}'\n");
        }
    }

    public function getBegin()
    {
        $begin = new Begin($this->driver->getDefaultSchema());
        $begin->setHome($this->currentPath['home']);
        return $begin;
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

    public function run()
    {
        Yentu::setMigrateCommand($this);
        self::fillOptions($this->options);

        $this->driver = ChangeLogger::wrap($this->manipulatorFactory->createManipulator(), $this->migrations, $this->io);
        $this->driver->setDumpQueriesOnly($this->options['dump-queries']);
        $this->driver->setDryRun($this->options['dry']);

        $totalOperations = 0;

        $filter = self::FILTER_UNRUN;
        $this->setupOptions($this->options, $filter);
        DatabaseItem::setDriver($this->driver);

        \yentu\Timer::start();
        $migrationPaths = $this->migrations->getAllPaths();
        
        foreach ($migrationPaths as $path) {
            $migrations = $this->filter($this->migrations->getMigrationFiles($path['home']), $filter);
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
        $this->io->pushOutputLevel(Io::OUTPUT_LEVEL_0);
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
                "SELECT count(*) as number_run FROM yentu_history WHERE migration = ? and version = ?", 
                array($migration['migration'], $migration['timestamp'])
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

    public function getChanges()
    {
        return $this->driver->getChanges();
    }
    
    public function getDefaultSchema()
    {
        return $this->driver->getDefaultSchema();
    }

    public function setRollbackCommand(Rollback $rollbackCommand)
    {
        $this->rollbackCommand = $rollbackCommand;
    }

    #[\Override]
    public function reverseActions()
    {
        if ($this->driver === null) {
            return;
        }

        $this->io->output("Attempting to reverse all changes ... ");
        if ($this->getChanges() > 0) {
            $this->io->pushOutputLevel(0);
            $this->rollbackCommand->run();
            $this->io->popOutputLevel();
        }
        $this->io->output("OK\n");
    }
}

