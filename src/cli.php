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

require "vendor/autoload.php";
require __DIR__ . "/../src/globals.php";

use clearice\ArgumentParser;
use clearice\io\Io;
use yentu\AbstractDatabaseManipulator;
use ntentan\atiaa\DriverFactory;
use yentu\Yentu;
use ntentan\config\Config;

$container = new ntentan\panie\Container();
$container->setup([
    Yentu::class => [Yentu::class, 'singleton' => true],
    Io::class => [Io::class, 'singleton' => true],
    Config::class => [
        function($container) {
            $yentu = $container->resolve(Yentu::class);
            $config = new Config();
            $configFile = $yentu->getPath("config/default.conf.php");
            if(file_exists($configFile)) {
                $config->readPath($yentu->getPath("config/default.conf.php"));
            }
            return $config;
        },
        'singleton' => true
    ],
    DriverFactory::class => [
        function($container) {
            $config = $container->resolve(Config::class)->get('db');                
            return new DriverFactory($config);
        }
    ],
    AbstractDatabaseManipulator::class => [
        function($container) {
            $config = $container->resolve(Config::class)->get('db');                
            $class = "\\yentu\\manipulators\\" . ucfirst($config['driver']);
            return $container->resolve($class, ['config' => $config]);                
        }, 
        'singleton' => true
    ]
]);

$io = $container->resolve(Io::class);
$argumentParser = $container->resolve(ArgumentParser::class);

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
    'command' => 'init',  'short_name' => 'p', 'name' => 'port',
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
    'command' => 'init', 'short' => 'p', 'long' => 'password',
    'help' => 'the password of the user on the target database', 'type' => 'string'
]);

$argumentParser->addOption([
    'command' => 'migrate', 'long' => 'no-foreign-keys',
    'help' => 'do not apply any database foriegn-key constraints'
]);
$argumentParser->addOption([
    'command' => 'migrate', 'long' => 'only-foreign-keys',
    'help' => 'apply only database foreign-key constraints (fails on errors)'
]);
$argumentParser->addOption([
    'command' => 'migrate', 'long' => 'force-foreign-keys',
    'help' => 'apply only database foreign-key constraints'
]);
$argumentParser->addOption([
    'command' => 'migrate', 'long' => 'dump-queries',
    'help' => 'just dump the query and perform no action'
]);
$argumentParser->addOption([
    'command' => 'migrate', 'long' => 'dry',
    'help' => 'perform a dry run. Do not alter the database in anyway'
]);
$argumentParser->addOption([
    'command' => 'migrate', 'long' => 'default-schema',
    'type' => 'string', 'help' => 'use this as the default schema for all migrations'
]);
$argumentParser->addOption([
    'command' => 'migrate', 'long' => 'default-ondelete',
    'help' => 'the default cascade action for foreign key deletes', 'type' => 'string'
]);
$argumentParser->addOption([
    'command' => 'migrate', 'long' => 'default-onupdate',
    'help' => 'the default cascade action for foreign key updates', 'type' => 'string'

]);

$argumentParser->addCommands(['name' => 'rollback', 'help' => 'reverse only migrations in this default-schema']);
$argumentParser->addOption([
    'short' => 'y', 'long' => 'home',
    'help' => 'specifies where the yentu configurations and migrations are found',
    'type' => 'string'
]);
$argumentParser->addOption([
    'short' => 'v', 'long' => 'verbose',
    'help' => 'set level of verbosity. high, mid, low and none',
    'type' => 'string'
]);
$argumentParser->addOption([
    'long' => 'details', 'help' => 'show details of all migrations.', 'command' => 'status'
]);

$argumentParser->setDescription("Yentu Database Migrations");
$argumentParser->setFootnote("Report bugs to jainooson@gmail.com");
$argumentParser->setUsage("[command] [options]");
$argumentParser->addHelp();
$argumentParser->setStrict(true);
$options = $argumentParser->parse($argv);

if (isset($options['verbose'])) {
    $io->setOutputLevel($options['verbose']);
}

try {
    $yentu = $container->resolve(Yentu::class);
    
    if (isset($options['__command__'])) {      
        $yentu->setDefaultHome($options['home'] ?? './yentu');

        $class = "\\yentu\\commands\\" . ucfirst($options['__command__']);
        unset($options['__command__']);
        $command = $container->resolve($class);
        $command->run($options);
    } else {
        $io->output($argumentParser->getHelpMessage($argv[0]));
    }
} catch (\yentu\exceptions\CommandException $e) {
    $io->resetOutputLevel();
    $io->error("Error! " . $e->getMessage() . "\n");
} catch (\ntentan\atiaa\exceptions\DatabaseDriverException $e) {
    $io->resetOutputLevel();
    $io->error("Database driver failed: " . $e->getMessage() . "\n");
    if(isset($command)) {
        $yentu->reverseCommand($command);
    }
} catch (\yentu\exceptions\DatabaseManipulatorException $e) {
    $io->resetOutputLevel();
    $io->error("Failed to perform database action: " . $e->getMessage() . "\n");
    if(isset($command)) {
        $yentu->reverseCommand($command);
    }
} catch (\ntentan\atiaa\DescriptionException $e) {
    $io->resetOutputLevel();
    $io->error("Failed to perform database action: " . $e->getMessage() . "\n");
    if(isset($command)) {
        $yentu->reverseCommand($command);
    }
} catch (\yentu\exceptions\SyntaxErrorException $e) {
    $io->resetOutputLevel();
    $io->error("Error found in syntax: {$e->getMessage()}\n");
    if(isset($command)) {
        $yentu->reverseCommand($command);
    }
} catch (\PDOException $e) {
    $io->resetOutputLevel();
    $io->error("Failed to connect to database: {$e->getMessage()}\n");
} catch (\ntentan\utils\exceptions\FileNotFoundException $e) {
    $io->resetOutputLevel();
    $io->error($e->getMessage() . "\n");
}
