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
        
        $migrations = scandir('yentu/migrations', SCANDIR_SORT_ASCENDING);
        $matches = array();
        
        foreach($migrations as $migration)
        {
            preg_match("/(?<timestamp>[0-9]{14})\_(?<migration>[a-z][a-z0-9\_]*)\.php/", $migration, $matches);
            
            ChangeLogger::setVersion($matches['timestamp']);
            ChangeLogger::setMigration($matches['migration']);
            
            if($matches['timestamp'] > $version)
            {
                echo "Applying '{$matches['migration']}' migration\n";
                require "yentu/migrations/{$migration}";
            }
        }
        
        DatabaseItem::commitPending();
    }
    
    public function schema($name)
    {
        DatabaseItem::commitPending();
        return DatabaseItem::create('schema', $name);
    }
}
