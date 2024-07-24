<?php

namespace yentu;

use clearice\io\Io;
use yentu\manipulators\AbstractDatabaseManipulator;

class ChangeLogger
{

    private $driver;
    private $version;
    private $migration;
    private $session;
    private $changes;
    private $expectedOperations = 1;
    private $operations;
    private static $defaultSchema = '';
    private $skippedItemTypes = array();
    private $allowedItemTypes = array();
    private $dumpQueriesOnly;
    private $dryRun;
    private $skipOnErrors = false;
    private $migrations;
    private $io;

    public function skip($itemType)
    {
        $this->skippedItemTypes[] = $itemType;
    }

    public function setDumpQueriesOnly($dumpQueriesOnly)
    {
        if ($dumpQueriesOnly === true) {
            $this->io->setOutputLevel(Io::OUTPUT_LEVEL_0);
        }
        $this->dumpQueriesOnly = $dumpQueriesOnly;
    }

    public function setDryRun($dryRun)
    {
        $this->dryRun = $dryRun;
    }

    public function allowOnly($itemType)
    {
        $this->allowedItemTypes[] = $itemType;
    }

    private function __construct(AbstractDatabaseManipulator $driver, Migrations $migrations, Io $io)
    {
        $this->session = sha1(rand() . time());
        $this->driver = $driver;
        $this->driver->createHistory();
        $this->migrations = $migrations;
        $this->io = $io;
    }

    public static function wrap(AbstractDatabaseManipulator $item, Migrations $migrations, Io $io) : ChangeLogger
    {
        return new ChangeLogger($item, $migrations, $io);
    }

    public function setVersion($version) : void
    {
        $this->version = $version;
    }

    public function setMigration($migration)
    {
        $this->migration = $migration;
    }

    private function performOperation($method, $matches, $arguments)
    {
        try {
            $return = $this->driver->$method($arguments[0]);
            $this->migrations->announce($matches['command'], $matches['item_type'], $arguments[0]);

            $this->driver->setDumpQuery(false);

            $this->io->pushOutputLevel(Io::OUTPUT_LEVEL_0);
            $this->driver->query(
                'INSERT INTO yentu_history(session, version, method, arguments, migration, default_schema) VALUES (?,?,?,?,?,?)', array(
                $this->session,
                $this->version,
                $method,
                json_encode($arguments),
                $this->migration,
                self::$defaultSchema
                )
            );
            $this->io->popOutputLevel();
            $this->changes++;
            $this->driver->setDisableQuery(false);
        } catch (\yentu\exceptions\DatabaseManipulatorException $e) {
            if ($this->skipOnErrors) {
                $this->io->output("E");
                $this->io->output("rror " . preg_replace("/([a-z])([A-Z])/", "$1 $2", $matches['item_type']) . " '" . $arguments[0]['name'] . "'\n", Io::OUTPUT_LEVEL_2);
            } else {
                throw $e;
            }
        }
        return $return;
    }

    public function __call($method, $arguments)
    {
        $return = null;
        if (preg_match("/^(?<command>add|drop|change|execute|reverse)(?<item_type>[a-zA-Z]+)/", $method, $matches)) {
            $this->driver->setDumpQuery($this->dumpQueriesOnly);
            $this->driver->setDisableQuery($this->dryRun);

            if (
                array_search($matches['item_type'], $this->skippedItemTypes) !== false ||
                (array_search($matches['item_type'], $this->allowedItemTypes) === false && count($this->allowedItemTypes) > 0)
            ) {
                $this->io->output("S");
                $this->io->output("kipping " . preg_replace("/([a-z])([A-Z])/", "$1 $2", $matches['item_type']) . " '" . (isset($arguments[0]['name']) ? $arguments[0]['name'] : null) . "'\n", Io::OUTPUT_LEVEL_2);
            } else {
                $return = $this->performOperation($method, $matches, $arguments);
            }

            $this->operations++;
            $this->outputProgress();
        } else if (preg_match("/^does([A-Za-z]+)/", $method)) {
            $invokable = new \ReflectionMethod($this->driver->getAssertor(), $method);
            return $invokable->invokeArgs($this->driver->getAssertor(), $arguments);
        } else {
            $invokable = new \ReflectionMethod($this->driver, $method);
            return $invokable->invokeArgs($this->driver, $arguments);
        }

        return $return;
    }

    public function outputProgress()
    {
        if ($this->expectedOperations > 0) {
            if ($this->operations % 74 === 0) {
                $this->io->output(sprintf("%4d%%\n", $this->operations / $this->expectedOperations * 100));
            } else {
                $this->io->output(sprintf("%4d%%\n", $this->operations / $this->expectedOperations * 100), Io::OUTPUT_LEVEL_2);
            }
        }
    }

    // public function setDefaultSchema($defaultSchema)
    // {
    //     self::$defaultSchema = $defaultSchema;
    // }

    public function getChanges()
    {
        return $this->changes;
    }

    public function resetOperations()
    {
        $operations = $this->operations;
        $this->operations = 0;
        return $operations;
    }

    public function __clone()
    {
        $this->driver = clone $this->driver;
    }

    public function setExpectedOperations($expectedOperations)
    {
        $this->expectedOperations = $expectedOperations;
    }

    public function setSkipOnErrors($skipOnErrors)
    {
        $this->skipOnErrors = $skipOnErrors;
    }

    public function getDefaultSchema()
    {
        return $this->driver->getDefaultSchema();
    }

}
