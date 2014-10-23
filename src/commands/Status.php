<?php
namespace yentu\commands;

use yentu\Command;
use yentu\Yentu;
use clearice\ClearIce;

/**
 * 
 */
class Status implements Command
{
    public function run($options)
    {
        if($options['details'])
        {
            ClearIce::setOutputLevel(Yentu::OUTPUT_LEVEL_2);
        }
        $driver = \yentu\DatabaseDriver::getConnection();
        $version = $driver->getVersion();
        
        if($version == null)
        {
            ClearIce::output("\nYou have not applied any migrations\n");
            return;
        }
        
        $migrationInfo = $this->getMigrationInfo($version);
        
        
        ClearIce::output("\n" . ($migrationInfo['counter']['previous'] == 0 ? 'No' : $migrationInfo['counter']['previous']) . " migration(s) have been applied so far.\n");
        $this->displayMigrations($migrationInfo['run']['previous']);
        
        ClearIce::output("\nLast migration applied:\n    {$migrationInfo['current']}\n");
        
        if($migrationInfo['counter']['yet'] > 0)
        {
            ClearIce::output("\nThere are {$migrationInfo['counter']['yet']} migration(s) that could be applied.\n");
            $this->displayMigrations($migrationInfo['run']['yet']);
        }
        else
        {
            ClearIce::output("\nThere are no pending migrations.\n");
        }
    }
    
    private function getMigrationInfo($version)
    {
        $migrations = Yentu::getMigrations();
        $counter['previous'] = 0;
        $counting = 'previous';
        $current = '';
        $run = array(
            'previous' => array(),
            'yet' => array()
        );
        
        foreach($migrations as $migration)
        {
            $migration = Yentu::getMigrationDetails($migration);
            $counter[$counting]++;
            $description = "{$migration['timestamp']} {$migration['migration']}";
            $run[$counting][] = $description;
            if($migration['timestamp'] == $version)
            {
                $current = $description;
                $counting = 'yet';
            }
        }
        
        return array(
            'counter' => $counter,
            'current' => $current,
            'run' => $run
        );
    }
    
    private function displayMigrations($descriptions)
    {
        foreach($descriptions as $description)
        {
            if(trim($description) == '') 
            {
                continue;
            }
            ClearIce::output("    $description\n", Yentu::OUTPUT_LEVEL_2);
        }        
    }
}
