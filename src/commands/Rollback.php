<?php

namespace yentu\commands;

use yentu\ChangeReverser;
use yentu\database\DatabaseItem;
use yentu\factories\DatabaseManipulatorFactory;
use clearice\io\Io;

class Rollback extends Command
{

    private $schemaCondition;
    private $schemaConditionData = [];
    private $manipulatorFactory;
    private $io;
    private $changeReverser;

    public function __construct(DatabaseManipulatorFactory $manipulatorFactory, Io $io, ChangeReverser $changeReverser)
    {
        $this->manipulatorFactory = $manipulatorFactory;
        $this->io = $io;
        $this->changeReverser = $changeReverser;
    }

    /**
     * @throws \yentu\exceptions\DatabaseManipulatorException
     */
    public function run()
    {
        $db = $this->manipulatorFactory->createManipulator();
        $this->changeReverser->setDriver($db);
        $previousMigration = '';
        
        if (isset($this->options['__args'])) {
            $operations = [];
            foreach ($this->options['__args'] ?? [] as $set) {
                $operations += $this->getOperations($db, $set);
            }
        } else {
            $session = $db->getLastSession();
            $operations = $db->query(
                "SELECT id, method, arguments, migration, default_schema FROM yentu_history WHERE $this->schemaCondition session = ? ORDER BY id DESC", $this->schemaConditionData + [$session]
            );
        }

        foreach ($operations as $operation) {
            if ($previousMigration !== $operation['migration']) {
                $this->io->output(
                    "Rolling back '{$operation['migration']}' migration" .
                    ($operation['default_schema'] != '' ? " on `{$operation['default_schema']}` schema." : ".") . "\n"
                );
                $previousMigration = $operation['migration'];
            }
            $this->changeReverser->call($operation['method'], json_decode($operation['arguments'], true));
            $db->query('DELETE FROM yentu_history WHERE id = ?', array($operation['id']));
        }
    }

    private function getOperations($db, $set)
    {
        $operations = [];
        if (preg_match("/[0-9]{14}/", $set)) {
            $operations = $db->query(
                "SELECT id, method, arguments, migration, default_schema FROM yentu_history WHERE $this->schemaCondition version = ? ORDER by id DESC", $this->schemaConditionData + [$set]
            );
        } elseif (preg_match("/[a-zA-Z\_\-]+/", $set)) {
            $operations = $db->query(
                "SELECT id, method, arguments, migration, default_schema FROM yentu_history WHERE $this->schemaCondition migration = ? ORDER by id DESC", $this->schemaConditionData + [$set]
            );
        }
        return $operations;
    }

}
