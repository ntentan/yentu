<?php
namespace yentu\commands;

use yentu\database\DatabaseItem;
use yentu\DatabaseManipulator;
use yentu\ChangeLogger;
use yentu\Yentu;
use yentu\database\Schema;
use yentu\database\Table;
use yentu\database\View;
use yentu\database\Query;
use clearice\ClearIce;

/**
 * The migrate command for the yentu database migration system. This class is
 * responsible for creating and updating items 
 */
class Migrate implements \yentu\Command
{
    private $driver;
    private $defaultSchema = false;
    private $lastSession;
    const FILTER_UNRUN = 'unrun';
    const FILTER_LAST_SESSION = 'lastSession';
    
    public function setupOptions($options, &$filter)
    {
        if(isset($options['ignore-foreign-keys']))
        {
            ClearIce::output("Ignoring all foreign key constraints ...\n");
            $this->driver->skip('ForeignKey');
        }
        
        if(isset($options['foreign-keys-only']))
        {
            ClearIce::output("Applying only foreign keys ...\n");
            $this->lastSession = $this->driver->getLastSession();
            $this->driver->allowOnly('ForeignKey');
            $filter = self::FILTER_LAST_SESSION;
        }            
        $this->setDefaultSchema($options);
    }
    
    private function setDefaultSchema($options)
    {
        if(isset($options['default-schema']))
        {
            $this->driver->setDefaultSchema($options['default-schema']);
            $this->defaultSchema = $options['default-schema'];
        }        
    }

    private function announceMigration($migrations, $path)
    {
        $size = count($migrations);
        if($size > 0)
        {
            if(isset($path['default-schema']))
            {
                $defaultSchema = $path['default-schema'];
            }
            ClearIce::output("Running $size migration(s) from '{$path['home']}'"); 
            if($defaultSchema != '')
            {
                ClearIce::output(" with '$defaultSchema' as the default schema.\n");
            }
        }  
        else
        {
            ClearIce::output("No migrations to run from '{$path['home']}'\n");
        }
    }

    public function run($options)
    {
        $this->driver = ChangeLogger::wrap(DatabaseManipulator::create());
        $this->driver->setDumpQueriesOnly($options['dump-queries']);
        $this->driver->setDryRun($options['dry']);
        
        Yentu::greet();
        
        $filter = self::FILTER_UNRUN;
        $this->setupOptions($options, $filter);
        DatabaseItem::setDriver($this->driver);
        
        foreach(Yentu::getMigrationPathsInfo() as $path)
        {
            $this->setDefaultSchema($path);
            $migrations = $this->filter(Yentu::getMigrations($path['home']), $filter);
            $this->announceMigration($migrations, $path);
            
            foreach($migrations as $migration)
            {
                ChangeLogger::setVersion($migration['timestamp']);
                ChangeLogger::setMigration($migration['migration']);                        
                ClearIce::output("\nApplying '{$migration['migration']}' migration\n");
                require "{$path['home']}/{$migration['file']}";
                DatabaseItem::purge();
                ClearIce::output("\n");
            }
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
                "SELECT count(*) as number_run FROM yentu_history WHERE migration = ? and version = ? and default_schema = ?", 
                array($migration['migration'], $migration['timestamp'], $this->defaultSchema)
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
        $versions = $this->driver->getSessionVersions($this->lastSession);
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
        $schema = new Schema($name);
        $schema->setIsReference(true);
        return $schema;
    }
    
    public function table($name)
    {
        DatabaseItem::purge();
        return new Table($name, new Schema($this->defaultSchema));
    }
    
    public function reftable($name)
    {
        $table = new Table($name, new Schema($this->defaultSchema));
        $table->setIsReference(true);
        return $table;
    }
    
    public function query($query, $bindData = array())
    {
        DatabaseItem::purge();
        return new Query($query, $bindData);
    }
    
    public function view($name)
    {
        DatabaseItem::purge();
        return new View($name, new Schema($this->defaultSchema));
    }
}
