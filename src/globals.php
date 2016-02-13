<?php

/*
 * The MIT License
 *
 * Copyright 2015 James Ekow Abaka Ainooson.
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

function variable($name)
{
    global $migrateVariables;

    if (isset($migrateVariables[$name])) {
        return $migrateVariables[$name];
    } else {
        throw new CommandException("Variable $name is undefined.");
    }
}

function variable_exists($name)
{
    global $migrateVariables;
    return isset($migrateVariables[$name]);
}
