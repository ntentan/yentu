<?php
namespace yentu\commands;

use yentu\database\DatabaseItem;
use yentu\DatabaseDriver;

class Migrate implements \yentu\Command
{
    public function run($options)
    {
        DatabaseItem::setDriver(DatabaseDriver::getConnection());
        require 'yentu/migrations/seed.php';
    }
    
    public function schema($name)
    {
        DatabaseItem::commitPending();
        return DatabaseItem::create('schema', $name);
    }
}
