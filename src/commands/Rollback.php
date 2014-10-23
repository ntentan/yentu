<?php
namespace yentu\commands;

use yentu\ChangeReverser;
use yentu\DatabaseManipulator;
use yentu\database\DatabaseItem;
use clearice\ClearIce;

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
                ClearIce::output("\nRolling back '{$operation['migration']}' migration\n");  
                $previousMigration = $operation['migration'];
            }
            try{
                ChangeReverser::call($operation['method'], json_decode($operation['arguments'], true));
            }
            catch(\yentu\DatabaseManipulatorException $e)
            {
                if($operation['method'] != 'addView')
                {
                    throw $e;
                }
            }
            $db->query('DELETE FROM yentu_history WHERE id = ?', array($operation['id']));
        }
    }
}
