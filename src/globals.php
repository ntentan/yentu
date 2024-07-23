<?php

use yentu\database\Table;
use yentu\database\Schema;
use yentu\exceptions\CommandException;

function begin()
{
    global $migrateCommand;
    return $migrateCommand->getBegin();
}

function refschema($name)
{
    $schema = new yentu\database\Schema($name);
    $schema->setIsReference(true);
    return $schema;
}

function reftable($name)
{
    global $defaultSchema;
    $table = new Table($name, new Schema($defaultSchema));
    $table->setIsReference(true);
    return $table;
}

// function variable($name)
// {
//     global $migrateVariables;

//     if (isset($migrateVariables[$name])) {
//         return $migrateVariables[$name];
//     } else {
//         throw new CommandException("Variable $name is undefined.");
//     }
// }

// function variable_exists($name)
// {
//     global $migrateVariables;
//     return isset($migrateVariables[$name]);
// }

