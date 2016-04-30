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

use clearice\ClearIce;
use yentu\Yentu;
use ntentan\config\Config;

ClearIce::addCommands(
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
);

ClearIce::addOptions(
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
);

ClearIce::addOptions(
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
);

ClearIce::addCommands(
    array(
        'command' => 'rollback',
        'long' => 'default-schema',
        'has_value' => true,
        'help' => 'reverse only migrations in this default-schema'
    )
);

ClearIce::addOptions(
    array(
        'short' => 'y',
        'long' => 'home',
        'help' => 'specifies where the yentu configurations are found',
        'has_value' => true
    ), 
    array(
        'short' => 'v',
        'long' => 'verbose',
        'help' => 'set level of verbosity. high, mid, low and none',
        'has_value' => true
    )
);

ClearIce::addOptions(
    array(
        'long' => 'details',
        'help' => 'show details of all migrations.',
        'command' => 'status'
    )
);

ClearIce::setDescription("Yentu Database Migrations\nVersion " . Yentu::getVersion());
ClearIce::setFootnote("Report bugs to jainooson@gmail.com");
ClearIce::setUsage("[command] [options]");
ClearIce::addHelp();
ClearIce::setStrict(true);
$options = ClearIce::parse();

if (isset($options['verbose'])) {
    ClearIce::setOutputLevel($options['verbose']);
}

try {
    if (isset($options['__command__'])) {
        if (isset($options['home'])) {
            Yentu::setDefaultHome($options['home']);
        }

        $class = "\\yentu\\commands\\" . ucfirst($options['__command__']);
        unset($options['__command__']);
        Config::readPath(Yentu::getPath('config'), 'yentu');
        $command = new $class();
        $command->run($options);
    } else {
        ClearIce::output(ClearIce::getHelpMessage());
    }
} catch (\yentu\exceptions\CommandException $e) {
    ClearIce::resetOutputLevel();
    ClearIce::error("Error! " . $e->getMessage() . "\n");
} catch (\ntentan\atiaa\exceptions\DatabaseDriverException $e) {
    ClearIce::resetOutputLevel();
    ClearIce::error("Database driver failed: " . $e->getMessage() . "\n");
    Yentu::reverseCommand($command);
} catch (\yentu\exceptions\DatabaseManipulatorException $e) {
    ClearIce::resetOutputLevel();
    ClearIce::error("Failed to perform database action: " . $e->getMessage() . "\n");
    Yentu::reverseCommand($command);
} catch (\ntentan\atiaa\DescriptionException $e) {
    ClearIce::resetOutputLevel();
    ClearIce::error("Failed to perform database action: " . $e->getMessage() . "\n");
    Yentu::reverseCommand($command);
} catch (\yentu\exceptions\SyntaxErrorException $e) {
    ClearIce::resetOutputLevel();
    ClearIce::error("Error found in syntax: {$e->getMessage()}\n");
    Yentu::reverseCommand($command);
} catch (\PDOException $e) {
    ClearIce::resetOutputLevel();
    ClearIce::error("Failed to connect to database: {$e->getMessage()}\n");
}
