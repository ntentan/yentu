<?php
namespace yentu\commands;

use yentu\ChangeReverser;
use yentu\DatabaseManipulator;
use yentu\database\DatabaseItem;
use yentu\Yentu;

class Rollback implements \yentu\Command
{
    public function run($options) 
    {
        $db = DatabaseManipulator::create();
        DatabaseItem::setDriver($db);
        ChangeReverser::setDriver($db);
        $previousMigration = '';
        
        $session = $db->getLastSession();
        $operations = $db->query(
            'SELECT id, method, arguments, migration FROM yentu_history WHERE session = ? ORDER BY id DESC',
            array(
                $session
            )
        );
        
        foreach($operations as $operation)
        {
            if($previousMigration !== $operation['migration'])
            {
                Yentu::out("\nRolling back '{$operation['migration']}' migration\n");  
                $previousMigration = $operation['migration'];
            }
            ChangeReverser::call($operation['method'], json_decode($operation['arguments'], true));
            $db->query('DELETE FROM yentu_history WHERE id = ?', array($operation['id']));
        }
    }
}
