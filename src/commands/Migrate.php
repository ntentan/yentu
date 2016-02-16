<?php
/* 
 * The MIT License
 *
 * Copyright 2014 James Ekow Abaka Ainooson
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace yentu\commands;

use yentu\database\DatabaseItem;
use yentu\DatabaseManipulator;
use yentu\ChangeLogger;
use yentu\Yentu;
use yentu\database\ForeignKey;

use clearice\ClearIce;

/**
 * The migrate command for the yentu database migration system. This class is
 * responsible for creating and updating items 
 */
class Migrate implements \clearice\Command, \yentu\Reversible
{
    private $driver;
    private $dryDriver;
    private $defaultSchema = false;
    private $lastSession;
    private $currentPath;
    const FILTER_UNRUN = 'unrun';
    const FILTER_LAST_SESSION = 'lastSession';
    
    public function setupOptions($options, &$filter)
    {
        if(isset($options['no-foreign-keys']))
        {
            ClearIce::output("Ignoring all foreign key constraints ...\n");
            $this->driver->skip('ForeignKey');
        }
        
        if(isset($options['only-foreign-keys']))
        {
            ClearIce::output("Applying only foreign keys ...\n");
            $this->lastSession = $this->driver->getLastSession();
            $this->driver->allowOnly('ForeignKey');
            $filter = self::FILTER_LAST_SESSION;
        }    
        
        if(isset($options['force-foreign-keys']))
        {
            ClearIce::output("Applying only foreign keys and skipping on errors ...\n");
            $this->lastSession = $this->driver->getLastSession();
            $this->driver->setSkipOnErrors($options['force-foreign-keys']);            
            $this->driver->allowOnly('ForeignKey');
            $filter = self::FILTER_LAST_SESSION;
        }
        
        if(isset($options['default-ondelete-action']))
        {
            ForeignKey::$defaultOnDelete = $options['default-ondelete-action'];
        }
        
        if(isset($options['default-onupdate-action']))
        {
            ForeignKey::$defaultOnUpdate = $options['default-onupdate-action'];
        }
        
        $this->setDefaultSchema($options);
    }
    
    private function setDefaultSchema($options)
    {
        global $defaultSchema;
        if(isset($options['default-schema']))
        {
            $this->driver->setDefaultSchema($options['default-schema']);
            $this->defaultSchema = $options['default-schema'];
            $defaultSchema = $this->defaultSchema;
        }        
    }

    private function announceMigration($migrations, $path)
    {
        $size = count($migrations);
        $defaultSchema = null;
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
    
    public function getBegin()
    {
        return new \yentu\database\Begin($this->defaultSchema);
    }
    
    private static function fillOptions(&$options)
    {
        if(!isset($options['dump-queries'])) {
            $options['dump-queries'] = false;
        }
        if(!isset($options['dry'])) {
            $options['dry'] = false;
        }
    }

    public function run($options=array())
    {
        global $migrateCommand;
        global $migrateVariables;
        
        self::fillOptions($options);
        
        $migrateCommand = $this;
        
        if($options['dump-queries'] !== true)
        {
            Yentu::greet();
        }
        
        $this->driver = ChangeLogger::wrap(DatabaseManipulator::create());
        $this->driver->setDumpQueriesOnly($options['dump-queries']);
        $this->driver->setDryRun($options['dry']);
        
        $totalOperations = 0;
                
        $filter = self::FILTER_UNRUN;
        $this->setupOptions($options, $filter);
        DatabaseItem::setDriver($this->driver);
                
        \yentu\Timer::start();
        $migrationPaths = Yentu::getMigrationPathsInfo();
        foreach($migrationPaths as $path)
        {
            $this->setDefaultSchema($path);
            $migrateVariables = $path['variables'];
            $migrations = $this->filter(Yentu::getMigrations($path['home']), $filter);
            $this->announceMigration($migrations, $path);
            $this->currentPath = $path;
            
            foreach($migrations as $migration)
            {
                $this->countOperations("{$path['home']}/{$migration['file']}");
                $this->driver->setVersion($migration['timestamp']);
                $this->driver->setMigration($migration['migration']);                        
                ClearIce::output("\nApplying '{$migration['migration']}' migration\n");
                require "{$path['home']}/{$migration['file']}";
                DatabaseItem::purge();
                ClearIce::output("\n");
                $totalOperations += $this->driver->resetOperations();
            }
        }
        
        if($this->driver->getChanges())
        {
            $elapsed = \yentu\Timer::stop();
            ClearIce::output("\nMigration took " . \yentu\Timer::pretty($elapsed) . "\n");
            ClearIce::output($this->driver->getChanges() . " operations performed\n");
            ClearIce::output($totalOperations - $this->driver->getChanges() . " operations skipped\n");
        }
        
        $this->driver->disconnect();
    }
    
    private function filter($migrations, $type = self::FILTER_UNRUN)
    {
        $filterMethod = "{$type}Filter";
        return $this->$filterMethod($migrations);
    }
    
    private function countOperations($migrationFile)
    {
        if($this->dryDriver === null)
        {
            $this->dryDriver = clone $this->driver;
            $this->dryDriver->setDryRun(true);
        }
        ClearIce::pushOutputLevel(ClearIce::OUTPUT_LEVEL_0);
        DatabaseItem::setDriver($this->dryDriver);
        require "$migrationFile";
        DatabaseItem::purge();
        DatabaseItem::setDriver($this->driver);        
        ClearIce::popOutputLevel();
        $this->driver->setExpectedOperations($this->dryDriver->resetOperations());
    }
    
    public function getCurrentPath()
    {
        return $this->currentPath;
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
    
    public function getChanges()
    {
        return $this->driver->getChanges();
    }
    
    public function reverse()
    {
        if($this->driver === null) 
        {
            return;
        }
        
        ClearIce::output("Attempting to reverse all changes ... ");
        if($this->getChanges() > 0)
        {
            ClearIce::pushOutputLevel(0);
            $rollback = new \yentu\commands\Rollback();
            $rollback->run(array());
            ClearIce::popOutputLevel();        
        }    
        ClearIce::output("OK\n");        
    }
}
