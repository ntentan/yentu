<?php
namespace yentu\commands;

use yentu\ChangeReverser;
use yentu\DatabaseDriver;
use yentu\database\DatabaseItem;

class Rollback extends \yentu\Command
{
    public function run($options) 
    {
        $db = DatabaseDriver::getConnection();
        DatabaseItem::setDriver($db);
        ChangeReverser::setDriver($db);
        
        $session = $db->getLastSession();
        $operations = $db->query(
            'SELECT id, method, arguments FROM yentu_history WHERE session = ? ORDER BY id DESC',
            array(
                $session
            )
        );
        
        foreach($operations as $operation)
        {
            ChangeReverser::call($operation['method'], json_decode($operation['arguments'], true));
            $db->query('DELETE FROM yentu_history WHERE id = ?', array($operation['id']));
        }
    }
}
