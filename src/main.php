<?php

// Choosing the right autoload between running within a PHAR or as a plain script.

$externalAutoload = __DIR__ . "/../../../../vendor/autoload.php";
if (file_exists($externalAutoload)) {
    require $externalAutoload;
} else {
    require __DIR__ . "/../vendor/autoload.php";
}

use clearice\argparser\ArgumentParser;
use clearice\io\Io;
use yentu\manipulators\AbstractDatabaseManipulator;
use yentu\Migrations;
use ntentan\config\Config;
use yentu\commands\Migrate;
use ntentan\panie\Container;
use yentu\Cli;
use yentu\commands\Command;

$container = new Container();
$container->setup(getContainerSettings());
$ui = $container->get(Cli::class);
$ui->run();

/**
 * Return the container setup.
 */
function getContainerSettings()
{
    return [
        '$dbConfig:array' => [
            function (Container $container) {
                $arguments = $container->get('$arguments:array');
                $configFile = $arguments['config-path'] ?? "yentu/yentu.ini";

                if (!file_exists($configFile)) {
                    $container->get(Io::class)->error("Failed to load the configuration file: $configFile\n");
                    exit(100);
                }

                $config = parse_ini_file($configFile, true);
                return $config['db'] ?? [];
            },
            'singleton' => true
        ],
        '$arguments:array' => [
            function (Container $container) {
                $argumentParser = $container->get(ArgumentParser::class);
                return $argumentParser->parse();
            },
            'singleton' => true
        ],
        Migrations::class => [Migrations::class, 'singleton' => true],
        Io::class => [Io::class, 'singleton' => true],
        AbstractDatabaseManipulator::class => [
            function ($container) {
                $config = $container->get(Config::class)->get('db');
                $class = "\\yentu\\manipulators\\" . ucfirst($config['driver']);
                return $container->get($class, ['config' => $config]);
            },
            'singleton' => true
        ],
        Migrate::class => [
            Migrate::class,
            'calls' => ['setRollbackCommand']
        ],
        ArgumentParser::class => [
            function (Container $container) {
                $io = $container->get(Io::class);
                $argumentParser = new ArgumentParser();

                // Setup commands
                $argumentParser->addCommand(['name' => 'import', 'help' => 'import the schema of an existing database']);
                $argumentParser->addCommand(['name' => 'migrate', 'help' => 'run new migrations on the target database']);
                $argumentParser->addCommand(['name' => 'init', 'help' => 'initialize the yentu directory']);
                $argumentParser->addCommand(['name' => 'create', 'help' => 'create a new migration']);
                $argumentParser->addCommand(['name' => 'rollback', 'help' => 'rollback the previus migration which was run']);
                $argumentParser->addCommand(['name' => 'status', 'help' => 'display the current status of the migrations']);

                // Setup options
                $argumentParser->addOption([
                    'command' => 'import', 'short_name' => 'd', 'name' => 'skip-defaults',
                    'help' => 'do not import the default values of the columns'
                ]);
                $argumentParser->addOption([
                    'command' => 'init', 'short_name' => 'i', 'name' => 'interractive',
                    'help' => 'initialize yentu interractively'
                ]);
                $argumentParser->addOption([
                    'command' => 'init', 'short_name' => 'd', 'name' => 'driver',
                    'help' => 'database platform. Supports postgresql', 'type' => 'string'
                ]);
                $argumentParser->addOption([
                    'command' => 'init', 'short_name' => 'h', 'name' => 'host',
                    'help' => 'the hostname of the target database', 'type' => 'string'
                ]);
                $argumentParser->addOption([
                    'command' => 'init', 'short_name' => 'P', 'name' => 'port',
                    'help' => 'the port of the target database', 'type' => 'string'
                ]);
                $argumentParser->addOption([
                    'command' => 'init', 'short_name' => 'n', 'name' => 'dbname',
                    'help' => 'the name of the target database', 'type' => 'string'
                ]);
                $argumentParser->addOption([
                    'command' => 'init', 'short_name' => 'u', 'name' => 'user',
                    'help' => 'the user name on the target database', 'type' => 'string'
                ]);
                $argumentParser->addOption([
                    'command' => 'init', 'short_name' => 'p', 'name' => 'password',
                    'help' => 'the password of the user on the target database', 'type' => 'string'
                ]);

                $argumentParser->addOption([
                    'command' => 'migrate', 'name' => 'no-foreign-keys',
                    'help' => 'do not apply any database foriegn-key constraints'
                ]);
                $argumentParser->addOption([
                    'command' => 'migrate', 'name' => 'only-foreign-keys',
                    'help' => 'apply only database foreign-key constraints (fails on errors)'
                ]);
                $argumentParser->addOption([
                    'command' => 'migrate', 'name' => 'force-foreign-keys',
                    'help' => 'apply only database foreign-key constraints'
                ]);
                $argumentParser->addOption([
                    'command' => 'migrate', 'name' => 'dump-queries',
                    'help' => 'just dump the query and perform no action'
                ]);
                $argumentParser->addOption([
                    'command' => 'migrate', 'name' => 'dry',
                    'help' => 'perform a dry run. Do not alter the database in anyway'
                ]);
                $argumentParser->addOption([
                    'command' => 'migrate', 'name' => 'default-ondelete',
                    'help' => 'the default cascade action for foreign key deletes', 'type' => 'string'
                ]);
                $argumentParser->addOption([
                    'command' => 'migrate', 'name' => 'default-onupdate',
                    'help' => 'the default cascade action for foreign key updates', 'type' => 'string'

                ]);
                $argumentParser->addOption([
                    'short_name' => 'y', 'name' => 'home',
                    'help' => 'specifies where the yentu configurations and migrations are found',
                    'type' => 'string'
                ]);
                $argumentParser->addOption([
                    'short_name' => 'v', 'name' => 'verbose',
                    'help' => 'set level of verbosity. high, mid, low and none',
                    'type' => 'string'
                ]);

                $argumentParser->addOption(['name' => 'details', 'help' => 'show details of all migrations.', 'command' => 'status']);
                $argumentParser->enableHelp("Yentu database migration tool", "Report bugs on https://github.com/ntentan/yentu");

                return $argumentParser;
            },
            'singleton' => true
        ],
        Command::class => [
            function ($container) {
                $arguments = $container->get('$arguments:array');
                if (isset($arguments['__command'])) {
                    $commandClass = "yentu\\commands\\" . ucfirst($arguments['__command']);
                    $command = $container->get($commandClass);
                    $command->setOptions($arguments);
                    return $command;
                } else {
                    return null;
                }
            }
        ]
    ];
}

 