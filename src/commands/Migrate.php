<?php
namespace yentu\commands;

use yentu\database\DatabaseItem;
use yentu\DatabaseManipulator;
use yentu\ChangeLogger;
use yentu\Yentu;
use yentu\database\Schema;
use yentu\database\Table;
use yentu\database\View;
use yentu\database\NullSchema;
use clearice\ClearIce;

class Migrate implements \yentu\Command
{
    private $driver;
    
    const FILTER_UNRUN = 'unrun';
    const FILTER_LAST_SESSION = 'lastSession';
    
    public function run($options)
    {
        $this->driver = ChangeLogger::wrap(DatabaseManipulator::create());
        // Set the dump queries early so it can suppress the subsequent greeting
        $this->driver->setDumpQueriesOnly($options['dump-queries']);
        
        Yentu::greet();
        
        $filter = self::FILTER_UNRUN;
        
        if(isset($options['ignore-foreign-keys']))
        {
            ClearIce::output("Ignoring all foreign key constraints ...\n");
            $this->driver->skip('ForeignKey');
        }
        
        if(isset($options['foreign-keys-only']))
        {
            ClearIce::output("Applying only foreign keys ...\n");
            $this->driver->allowOnly('ForeignKey');
            $filter = self::FILTER_LAST_SESSION;
        }
        
        DatabaseItem::setDriver($this->driver);
        
        $migrations = $this->filter(Yentu::getMigrations(), $filter);
        
        foreach($migrations as $migration)
        {
            ChangeLogger::setVersion($migration['timestamp']);
            ChangeLogger::setMigration($migration['migration']);                        
            ClearIce::output("\nApplying '{$migration['migration']}' migration\n");
            require Yentu::getPath("migrations/{$migration['file']}");
            DatabaseItem::purge();
            ClearIce::output("\n");
        }
        
        $this->driver->disconnect();
    }
    
    private function filter($migrations, $type = self::FILTER_UNRUN)
    {
        $filterMethod = "{$type}Filter";
        return $this->$filterMethod($migrations);
    }
    
    private function unrunFilter($input)
    {
        $output = array();
        foreach($input as $migration)
        {
            $run = $this->driver->query(
                "SELECT count(*) as number_run FROM yentu_history WHERE migration = ? and version = ?", 
                array($migration['migration'], $migration['timestamp'])
            );
            
            if($run[0]['number_run'] == 0)
            {
                $output[] = $migration;
            }
        }
        return $output;
    }
    
    private function lastSessionFilter($input)
    {
        $versions = $this->driver->getSessionVersions($this->driver->getLastSession());
        $output = array();
        foreach($input as $migration)
        {
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
        return new View($name, new NullSchema());
    }
}
