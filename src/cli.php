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
use yentu\AbstractDatabaseManipulator;
use ntentan\atiaa\DriverFactory;
use yentu\Yentu;
use ntentan\config\Config;

$argumentParser = new ArgumentParser();
$argumentParser->addCommands([
    array(
        'command' => 'import',
        'help' => 'import the schema of an existing database'
    ), 
    array(
        'command' => 'migrate',
        'help' => 'run new migrations on the target database'
    ), 
    array(
        'command' => 'init',
        'help' => 'initialize the yentu directory'
    ), 
    array(
        'command' => 'create',
        'usage' => 'create [name] [options]..',
        'help' => 'create a new migration'
    ), 
    array(
        'command' => 'rollback',
        'help' => 'rollback the previus migration which was run'
    ), 
    array(
        'command' => 'status',
        'help' => 'display the current status of the migrations'
    )
]);

$argumentParser->addOptions([
    array(
        'command' => 'import',
        'short' => 'd',
        'long' => 'skip-defaults',
        'help' => 'do not import the default values of the columns'
    ), 
    array(
        'command' => 'init',
        'short' => 'i',
        'long' => 'interractive',
        'help' => 'initialize yentu interractively'
    ), 
    array(
        'command' => 'init',
        'short' => 'd',
        'long' => 'driver',
        'help' => 'database platform. Supports postgresql',
        'has_value' => true
    ), 
    array(
        'command' => 'init',
        'short' => 'h',
        'long' => 'host',
        'help' => 'the hostname of the target database',
        'has_value' => true
    ), 
    array(
        'command' => 'init',
        'short' => 'p',
        'long' => 'port',
        'help' => 'the port of the target database',
        'has_value' => true
    ), 
    array(
        'command' => 'init',
        'short' => 'n',
        'long' => 'dbname',
        'help' => 'the name of the target database',
        'has_value' => true
    ), 
    array(
        'command' => 'init',
        'short' => 'u',
        'long' => 'user',
        'help' => 'the user name on the target database',
        'has_value' => true
    ), 
    array(
        'command' => 'init',
        'short' => 'p',
        'long' => 'password',
        'help' => 'the passwrd of the user on the target database',
        'has_value' => true
    )
]);

$argumentParser->addOptions([
    array(
        'command' => 'migrate',
        'long' => 'no-foreign-keys',
        'help' => 'do not apply any database foriegn-key constraints'
    ),
    array(
        'command' => 'migrate',
        'long' => 'only-foreign-keys',
        'help' => 'apply only database foreign-key constraints (fails on errors)'
    ), 
    array(
        'command' => 'migrate',
        'long' => 'force-foreign-keys',
        'help' => 'apply only database foreign-key constraints'
    ), 
    array(
        'command' => 'migrate',
        'long' => 'dump-queries',
        'help' => 'just dump the query and perform no action'
    ), 
    array(
        'command' => 'migrate',
        'long' => 'dry',
        'help' => 'perform a dry run. Do not alter the database in anyway'
    ), 
    array(
        'command' => 'migrate',
        'long' => 'default-schema',
        'has_value' => true,
        'help' => 'use this as the default schema for all migrations'
    ), 
    array(
        'command' => 'migrate',
        'long' => 'default-ondelete',
        'help' => 'the default cascade action for foreign key deletes',
        'has_value' => true
    ), 
    array(
        'command' => 'migrate',
        'long' => 'default-onupdate',
        'help' => 'the default cascade action for foreign key updates',
        'has_value' => true
    )
]);

$argumentParser->addCommands([
    array(
        'command' => 'rollback',
        'long' => 'default-schema',
        'has_value' => true,
        'help' => 'reverse only migrations in this default-schema'
    )
]);

$argumentParser->addOptions([
    array(
        'short' => 'y',
        'long' => 'home',
        'help' => 'specifies where the yentu configurations and migrations are found',
        'has_value' => true
    ), 
    array(
        'short' => 'v',
        'long' => 'verbose',
        'help' => 'set level of verbosity. high, mid, low and none',
        'has_value' => true
    ),
    array(
        'long' => 'details',
        'help' => 'show details of all migrations.',
        'command' => 'status'
    )    
]);

$argumentParser->setDescription("Yentu Database Migrations");
$argumentParser->setFootnote("Report bugs to jainooson@gmail.com");
$argumentParser->setUsage("[command] [options]");
$argumentParser->addHelp();
$argumentParser->setStrict(true);
$options = $argumentParser->parse();

if (isset($options['verbose'])) {
    $argumentParser->setOutputLevel($options['verbose']);
}

try {
    $container = new ntentan\panie\Container();
    $container->setup([
        Yentu::class => [
            Yentu::class, 'calls' => ['setDefaultHome' => ['home' => $options['home'] ?? './yentu']], 
            'singleton' => true
        ],
        clearice\ConsoleIO::class => [clearice\ConsoleIO::class, 'singleton' => true],
        Config::class => [
            function($container) {
                $yentu = $container->resolve(Yentu::class);
                $config = new Config();
                $config->readPath($yentu->getPath("config/default.conf.php"));
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
    $yentu = $container->resolve(Yentu::class);
    
    if (isset($options['__command__'])) {      
        if (isset($options['home'])) {
            $yentu->setDefaultHome($options['home']);
        }

        $class = "\\yentu\\commands\\" . ucfirst($options['__command__']);
        unset($options['__command__']);
        $command = $container->resolve($class);
        $command->run($options);
    } else {
        $argumentParser->output($argumentParser->getHelpMessage());
    }
} catch (\yentu\exceptions\CommandException $e) {
    $argumentParser->resetOutputLevel();
    $argumentParser->error("Error! " . $e->getMessage() . "\n");
} catch (\ntentan\atiaa\exceptions\DatabaseDriverException $e) {
    $argumentParser->resetOutputLevel();
    $argumentParser->error("Database driver failed: " . $e->getMessage() . "\n");
    $yentu->reverseCommand($command);
} catch (\yentu\exceptions\DatabaseManipulatorException $e) {
    $argumentParser->resetOutputLevel();
    $argumentParser->error("Failed to perform database action: " . $e->getMessage() . "\n");
    $yentu->reverseCommand($command);
} catch (\ntentan\atiaa\DescriptionException $e) {
    $argumentParser->resetOutputLevel();
    $argumentParser->error("Failed to perform database action: " . $e->getMessage() . "\n");
    $yentu->reverseCommand($command);
} catch (\yentu\exceptions\SyntaxErrorException $e) {
    $argumentParser->resetOutputLevel();
    $argumentParser->error("Error found in syntax: {$e->getMessage()}\n");
    $yentu->reverseCommand($command);
} catch (\PDOException $e) {
    $argumentParser->resetOutputLevel();
    $argumentParser->error("Failed to connect to database: {$e->getMessage()}\n");
} catch (\ntentan\utils\exceptions\FileNotFoundException $e) {
    $argumentParser->resetOutputLevel();
    $argumentParser->error($e->getMessage() . "\n");
}
