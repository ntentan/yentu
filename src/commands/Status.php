<?php

namespace yentu\commands;


/**
 *
 */
class Status extends Command
{
    public function run()
    {
        $this->yentu->greet();
        $driver = $this->manipulatorFactory->createManipulator();
        $version = $driver->getVersion();

        if ($version == null) {
            $this->io->output("\nYou have not applied any migrations\n");
            return;
        }

        $migrationInfo = $this->getMigrationInfo();

        $this->io->output("\n" . ($migrationInfo['counter']['previous'] == 0 ? 'No' : $migrationInfo['counter']['previous']) . " migration(s) have been applied so far.\n");
        $this->displayMigrations($migrationInfo['run']['previous']);

        $this->io->output("\nLast migration applied:\n    {$migrationInfo['current']}\n");

        if ($migrationInfo['counter']['yet'] > 0) {
            $this->io->output("\n{$migrationInfo['counter']['yet']} migration(s) that could be applied.\n");
            $this->displayMigrations($migrationInfo['run']['yet']);
        } else {
            $this->io->output("\nThere are no pending migrations.\n");
        }
    }

    private function getMigrationInfo()
    {
        $runMigrations = $this->yentu->getRunMirations();
        $migrations = $this->yentu->getAllMigrations();

        $counter['previous'] = count($runMigrations);
        end($runMigrations);
        $current = "{$runMigrations[key($runMigrations)]['timestamp']} {$runMigrations[key($runMigrations)]['migration']}";
        $run = array(
            'previous' => array(),
            'yet' => array()
        );

        foreach ($runMigrations as $timestamp => $migration) {
            unset($migrations[$timestamp]);
            $run['previous'][] = "{$timestamp} {$migration['migration']} " .
                ($migration['default_schema'] == '' ? '' : "on `{$migration['default_schema']}` schema");
        }

        foreach ($migrations as $timestamp => $migration) {
            $run['yet'][] = "{$timestamp} {$migration['migration']}";
        }
        $counter['yet'] = count($run['yet']);

        return array(
            'counter' => $counter,
            'current' => $current,
            'run' => $run
        );
    }

    private function displayMigrations($descriptions)
    {
        foreach ($descriptions as $description) {
            if (trim($description) == '') {
                continue;
            }
            $this->io->output("    $description\n");
        }
    }

}
