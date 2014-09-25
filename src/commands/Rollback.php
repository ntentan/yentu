<?php
namespace yentu\commands;

use yentu\ChangeReverser;
use yentu\DatabaseDriver;
use yentu\database\DatabaseItem;
use yentu\Yentu;

class Rollback implements \yentu\Command
{
    public function run($options) 
    {
        $db = DatabaseDriver::getConnection();
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
            }
            ChangeReverser::call($operation['method'], json_decode($operation['arguments'], true));
            $db->query('DELETE FROM yentu_history WHERE id = ?', array($operation['id']));
        }
    }
}
