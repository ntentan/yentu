<?php
namespace yentu\commands;

use yentu\database\DatabaseItem;
use yentu\DatabaseDriver;
use yentu\ChangeLogger;

class Migrate implements \yentu\Command
{
    public function run($options)
    {
        $db = ChangeLogger::wrap(DatabaseDriver::getConnection());
        DatabaseItem::setDriver($db);
        
        $version = $db->getVersion();
        
        $migrations = scandir('yentu/migrations', 0);
        $matches = array();
        
        foreach($migrations as $migration)
        {
            preg_match("/(?<timestamp>[0-9]{14})\_(?<migration>[a-z][a-z0-9\_]*)\.php/", $migration, $matches);
            
            if($matches['timestamp'] > $version)
            {
                ChangeLogger::setVersion($matches['timestamp']);
                ChangeLogger::setMigration($matches['migration']);                        
                echo "Applying '{$matches['migration']}' migration\n";
                require "yentu/migrations/{$migration}";
                DatabaseItem::purge();
            }
        }
        
        //DatabaseItem::commitPending();
    }
    
    public function schema($name)
    {
        DatabaseItem::purge();
        return new \yentu\database\Schema($name);
    }
    
    public function refSchema($name)
    {
        return new \yentu\database\Schema($name);
    }
}
