<?php
namespace yentu\commands;

class Migrate implements \yentu\Command
{
    public function run()
    {
        database\DatabaseItem::setDriver(DatabaseDriver::getConnection());
        require 'yentu/migrations/seed.php';
    }
    
    public function schema($schemaName)
    {
        return new database\Schema($schemaName);
    }
}
