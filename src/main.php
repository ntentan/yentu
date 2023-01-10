<?php

/*
 * The MIT License
 *
 * Copyright 2015 ekow.
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

$externalAutoload = __DIR__ . "/../../../../vendor/autoload.php";

if (file_exists($externalAutoload)) {
    require $externalAutoload;
} else {
    require __DIR__ . "/../vendor/autoload.php";    
}
require_once __DIR__ . "/../src/globals.php";

use clearice\argparser\ArgumentParser;
use clearice\io\Io;
use yentu\manipulators\AbstractDatabaseManipulator;
use ntentan\atiaa\DriverFactory;
use yentu\Migrations;
use ntentan\config\Config;
use yentu\commands\Migrate;
use ntentan\panie\Container;
use yentu\Cli;
use yentu\commands\Command;

$container = new Container();
$container->setup(get_container_settings());
$ui = $container->resolve(Cli::class);
$ui->run();

/**
 * Return the container setup.
 */
function get_container_settings() {
    return [
        Migrations::class => [Migrations::class, 'singleton' => true],
        Io::class => [Io::class, 'singleton' => true],
        Config::class => [
            'singleton' => true
        ],
        DriverFactory::class => [ 
            function($container) {
                return new DriverFactory($container->resolve(Config::class)->get('db'));        
            }, 
            'singleton' => true
        ],
        AbstractDatabaseManipulator::class => [
            function ($container) {
                $config = $container->resolve(Config::class)->get('db');
                $class = "\\yentu\\manipulators\\" . ucfirst($config['driver']);
                return $container->resolve($class, ['config' => $config]);
            },
            'singleton' => true
        ],
        Migrate::class => [
            Migrate::class,
            'calls' => ['setRollbackCommand']
        ],
        ArgumentParser::class => [
            function($container) {
                $io = $container->resolve(Io::class);
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
                    'command' => 'migrate', 'name' => 'default-schema',
                    'type' => 'string', 'help' => 'use this as the default schema for all migrations'
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
            function($container)
            {
                /**
                 * @var Container $container
                 * @var Migrations $migrations
                 */

                $argumentParser = $container->resolve(ArgumentParser::class);
                $arguments = $argumentParser->parse();
                if(isset($arguments['__command'])) {
                    $commandClass = "yentu\\commands\\" . ucfirst($arguments['__command']);
                    $defaultHome = $arguments['home'] ?? './yentu';
                    $configFile = "{$defaultHome}/config/default.conf.php";

                    if(file_exists($configFile)) {
                        $config = $container->resolve(Config::class);
                        $config->readPath($configFile);

                        $migrations = $container->resolve(
                            Migrations::class, [
                                'config' => [
                                    'variables' => $config->get('variables', []),
                                    'other_migrations' => $config->get('other_migrations', []),
                                    'home' => $defaultHome
                                ]
                            ]
                        );
                    }

                    $command = $container->resolve($commandClass);
                    $command->setOptions($arguments);
                    return $command;
                } else {
                    return null;
                }
            }
        ]
    ];
}

 