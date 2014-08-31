<?php
namespace yentu\commands;

use yentu\database\DatabaseItem;
use yentu\DatabaseDriver;
use yentu\ChangeLogger;
use yentu\Yentu;
use yentu\database\Schema;
use yentu\database\Table;
use yentu\database\View;
use yentu\database\NullSchema;

class Migrate implements \yentu\Command
{
    private $driver;
    
    public function run($options)
    {
        $this->driver = ChangeLogger::wrap(DatabaseDriver::getConnection());
        DatabaseItem::setDriver($this->driver);
        
        $version = $this->driver->getVersion();
        
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
        return new Schema($name);
    }
    
    public function refschema($name)
    {
        return new Schema($name);
    }
    
    public function table($name)
    {
        DatabaseItem::purge();
        return new Table($name, new NullSchema());
    }
    
    public function reftable($name)
    {
        return new Table($name, new NullSchema());
    }
    
    public function query($query, $bindData = array())
    {
        DatabaseItem::purge();
        return $this->driver->query($query, $bindData);
    }
    
    public function view($name)
    {
        DatabaseItem::purge();
        return new View($name, new NullSchema);
    }
}
