<?php

namespace yentu\commands;

use yentu\ChangeReverser;
use yentu\database\DatabaseItem;
use yentu\factories\DatabaseManipulatorFactory;
use clearice\io\Io;

class Rollback implements CommandInterface
{

    private $schemaCondition;
    private $schemaConditionData = [];
    private $manipulatorFactory;
    private $io;

    public function __construct(DatabaseManipulatorFactory $manipulatorFactory, Io $io)
    {
        $this->manipulatorFactory = $manipulatorFactory;
        $this->io = $io;
    }

    /**
     * @param array $options
     * @throws \yentu\exceptions\DatabaseManipulatorException
     */
    public function run($options = array())
    {
        $db = $this->manipulatorFactory->createManipulator();
        DatabaseItem::setDriver($db);
        ChangeReverser::setDriver($db);
        $previousMigration = '';

        if (isset($options['default-schema'])) {
            $this->schemaCondition = "default_schema = ?";
            $this->schemaConditionData[] = $options['default-schema'];
        }

        if (isset($options['__args'])) {
            $operations = [];
            foreach ($options['__args'] ?? [] as $set) {
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
            ChangeReverser::call($operation['method'], json_decode($operation['arguments'], true));
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
