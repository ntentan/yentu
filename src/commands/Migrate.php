<?php
namespace yentu\commands;

use yentu\database\DatabaseItem;
use yentu\DatabaseDriver;
use yentu\ChangeLogger;
use yentu\Yentu;

class Migrate implements \yentu\Command
{
    public function run($options)
    {
        $db = ChangeLogger::wrap(DatabaseDriver::getConnection());
        DatabaseItem::setDriver($db);
        
        $version = $db->getVersion();
        
        $migrations = scandir(Yentu::getPath('migrations'), 0);
        $matches = array();
        
        foreach($migrations as $migration)
        {
            preg_match("/(?<timestamp>[0-9]{14})\_(?<migration>[a-z][a-z0-9\_]*)\.php/", $migration, $matches);
            
            if($matches['timestamp'] > $version)
            {
                ChangeLogger::setVersion($matches['timestamp']);
                ChangeLogger::setMigration($matches['migration']);                        
                Yentu::out("Applying '{$matches['migration']}' migration\n");
                require Yentu::getPath("migrations/{$migration}");
                DatabaseItem::purge();
            }
        }
    }
    
    public function schema($name)
    {
        DatabaseItem::purge();
        return new \yentu\database\Schema($name);
    }
    
    public function refschema($name)
    {
        return new \yentu\database\Schema($name);
    }
    
    public function table($name)
    {
        DatabaseItem::purge();
        return new \yentu\database\Table($name, new \yentu\database\NullSchema());
    }
    
    public function reftable($name)
    {
        return new \yentu\database\Table($name, new \yentu\database\NullSchema());
    }
}
