<?php
namespace yentu\commands;

use yentu\database\DatabaseItem;
use yentu\DatabaseDriver;

class Migrate implements \yentu\Command
{
    public function run($options)
    {
        $db = DatabaseDriver::getConnection();
        DatabaseItem::setDriver($db);
        require 'yentu/migrations/seed.php';
        DatabaseItem::commitPending();
    }
    
    public function schema($name)
    {
        DatabaseItem::commitPending();
        return DatabaseItem::create('schema', $name);
    }
}
