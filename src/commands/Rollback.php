<?php
namespace yentu\commands;

use yentu\ChangeReverser;
use yentu\DatabaseManipulator;
use yentu\database\DatabaseItem;
use clearice\ClearIce;
use yentu\Yentu;

class Rollback implements \clearice\Command
{
    public function run($options=array()) 
    {
        Yentu::greet();
        $db = DatabaseManipulator::create();
        DatabaseItem::setDriver($db);
        ChangeReverser::setDriver($db);
        $previousMigration = '';
        
        $session = $db->getLastSession();
        
        $operations = $db->query(
            'SELECT id, method, arguments, migration, default_schema FROM yentu_history WHERE session = ? ORDER BY id DESC',
            array($session)
        );
        
        foreach($operations as $operation)
        {
            if($previousMigration !== $operation['migration'])
            {
                ClearIce::output(
                    "Rolling back '{$operation['migration']}' migration" . 
                    ($operation['default_schema'] != '' ? " on `{$operation['default_schema']}` schema." : ".") . "\n"
                );  
                $previousMigration = $operation['migration'];
            }
            ChangeReverser::call($operation['method'], json_decode($operation['arguments'], true));
            $db->query('DELETE FROM yentu_history WHERE id = ?', array($operation['id']));
        }
    }
}
