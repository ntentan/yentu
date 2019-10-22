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

use yentu\Parameters;
use yentu\Reversible;
use yentu\exceptions\CommandException;

/**
 * The init command class. This command intiates a project for yentu migration
 * by creating the required directories and configuration files. It also goes
 * ahead to create the history table which exists in the database.
 */
class Init implements Reversible, CommandInterface
{
    private function getParams()
    {
        if (isset($this->options['interractive'])) {
            $params['driver'] = $this->io->getResponse('Database type', ['required' => true, 'answers' => ['postgresql', 'mysql', 'sqlite']]);

            if ($params['driver'] === 'sqlite') {
                $params['file'] = $this->io->getResponse('Database file', ['required' => true]);
            } else {
                $params['host'] = $this->io->getResponse('Database host', ['default' => 'localhost']);
                $params['port'] = $this->io->getResponse('Database port');
                $params['dbname'] = $this->io->getResponse('Database name',['required' => true]);
                $params['user'] = $this->io->getResponse('Database user name', ['required' => true]);
                $params['password'] = $this->io->getResponse('Database password', ['required' => FALSE]);
            }
        } else {
            $params = [];
            foreach(['driver', 'file', 'host', 'port', 'dbname', 'user', 'password'] as $key) {
                if(isset($this->options[$key])) {
                    $params[$key] = $this->options[$key];
                }
            }
        }
        return $params;
    }

    public function createConfigFile($params)
    {
        $params = Parameters::wrap(
                $params, ['port', 'file', 'host', 'dbname', 'user', 'password']
        );
        mkdir($this->yentu->getPath(''));
        mkdir($this->yentu->getPath('config'));
        mkdir($this->yentu->getPath('migrations'));

        $configFile = new \yentu\CodeWriter();
        $configFile->add('return [');
        $configFile->addIndent();
        $configFile->add("'db' => [");
        $configFile->addIndent();
        $configFile->add("'driver' => '{$params['driver']}',");
        $configFile->add("'host' => '{$params['host']}',");
        $configFile->add("'port' => '{$params['port']}',");
        $configFile->add("'dbname' => '{$params['dbname']}',");
        $configFile->add("'user' => '{$params['user']}',");
        $configFile->add("'password' => '{$params['password']}',");
        $configFile->add("'file' => '{$params['file']}',");
        $configFile->decreaseIndent();
        $configFile->add(']');
        $configFile->decreaseIndent();
        $configFile->add('];');

        file_put_contents($this->yentu->getPath("config/default.conf.php"), $configFile);
        return $params;
    }

    /**
     * @throws CommandException
     */
    public function run()
    {
        $this->yentu->greet();
        $home = $this->yentu->getPath('');
        if (file_exists($home)) {
            throw new CommandException("Could not initialize yentu. Your project has already been initialized with yentu.");
        } else if (!is_writable(dirname($home))) {
            throw new CommandException("Your home directory ($home) could not be created.");
        }

        $params = $this->getParams();
        
        if (count($params) == 0 && defined('STDOUT')) {
            throw new CommandException(
                "You didn't provide any parameters for initialization. Please execute yentu "
                . "with `init -i` to initialize yentu interractively. "
                . "You can also try `init --help` for more information."
            );
        }

        $config = $this->createConfigFile($params);
        $db = $this->manipulatorFactory->createManipulatorWithConfig($config);

        if ($db->getAssertor()->doesTableExist('yentu_history')) {
            throw new CommandException("Could not initialize yentu. Your database has already been initialized with yentu.");
        }

        $db->createHistory();
        $db->disconnect();

        $this->io->output("Yentu successfully initialized.\n");
    }

    public function reverse()
    {
        unlink($this->yentu->getPath("config/default.conf.php"));
        rmdir($this->yentu->getPath("config"));
        rmdir($this->yentu->getPath("migrations"));
        rmdir($this->yentu->getPath(""));
    }

}
