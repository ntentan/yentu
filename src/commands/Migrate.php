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
    
    const FILTER_UNRUN = 'unrun';
    const FILTER_LAST_SESSION = 'lastSession';
    
    public function run($options)
    {
        $this->driver = ChangeLogger::wrap(DatabaseDriver::getConnection());
        $filter = self::FILTER_UNRUN;
        
        if(isset($options['ignore-foreign-keys']))
        {
            Yentu::out("\nIgnoring all foreign key constraints ...\n");
            $this->driver->skip('ForeignKey');
        }
        
        if(isset($options['foreign-keys-only']))
        {
            Yentu::out("\nApplying only foreign keys ...\n");
            $this->driver->allowOnly('ForeignKey');
            $filter = self::FILTER_LAST_SESSION;
        }
        
        DatabaseItem::setDriver($this->driver);
        
        $version = $this->driver->getVersion();
        
        $migrations = $this->filter(Yentu::getMigrations(), $filter, $version);
        
        foreach($migrations as $migration)
        {
            ChangeLogger::setVersion($migration['timestamp']);
            ChangeLogger::setMigration($migration['migration']);                        
            Yentu::out("\nApplying '{$migration['migration']}' migration\n");
            require Yentu::getPath("migrations/{$migration['file']}");
            DatabaseItem::purge();
            Yentu::out("\n");
        }
    }
    
    private function filter($migrations, $type = self::FILTER_UNRUN, $version = null)
    {
        $filterMethod = "{$type}Filter";
        return $this->$filterMethod($migrations, $version);
    }
    
    private function unrunFilter($input, $version)
    {
        $output = array();
        foreach($input as $migration)
        {
            $migration = Yentu::getMigrationDetails($migration);
            if($migration['timestamp'] > $version)
            {
                $output[] = $migration;
            }
        }
        return $output;
    }
    
    private function lastSessionFilter($input, $version)
    {
        $versions = $this->driver->getSessionVersions($this->driver->getLastSession());
        $output = array();
        foreach($input as $migration)
        {
            $migration = Yentu::getMigrationDetails($migration);
            if(array_search($migration['timestamp'], $versions) !== false)
            {
                $output[] = $migration;
            }
        }
        return $output;
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
