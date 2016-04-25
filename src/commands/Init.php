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

use clearice\Command;
use yentu\Yentu;
use clearice\ClearIce;
use yentu\exceptions\CommandException;
use ntentan\config\Config;

/**
 * The init command class. This command intiates a project for yentu migration
 * by creating the required directories and configuration files. It also goes
 * ahead to create the history table which exists in the database.
 */
class Init implements Command
{
    private function getParams($options)
    {
        if(isset($options['interractive']))
        {   
            $params['driver'] = ClearIce::getResponse('Database type', array(
                    'required' => true,
                    'answers' => array(
                        'postgresql',
                        'mysql',
                        'sqlite'
                    )
                )
            );
            
            if($params['driver'] === 'sqlite')
            {
                $params['file'] = ClearIce::getResponse('Database file',[
                    'required' => true
                ]);
            }
            else
            {
                $params['host'] = ClearIce::getResponse('Database host', array(
                        'required' => true,
                        'default' => 'localhost'
                    )
                );

                $params['port'] = ClearIce::getResponse('Database port');

                $params['dbname'] = ClearIce::getResponse('Database name', array(
                        'required' => true
                    )
                );

                $params['user'] = ClearIce::getResponse('Database user name', array(
                        'required' => true
                    )
                );            

                $params['password'] = ClearIce::getResponse('Database password', array(
                        'required' => FALSE
                    )
                );
            }
        }
        else
        {
            $params = $options;
        }
        return $params;
    }
    
    public function createConfigFile($params)
    {
        $params = \yentu\Parameters::wrap(
            $params,
            ['port', 'file', 'host', 'dbname', 'user', 'password']
        );
        mkdir(Yentu::getPath(''));
        mkdir(Yentu::getPath('config'));
        mkdir(Yentu::getPath('migrations'));
        
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
        
        file_put_contents(Yentu::getPath("config/default.conf.php"), $configFile);        
    }
    
    public function run($options=array())
    {
        Yentu::greet();
        if(file_exists(Yentu::getPath('')))
        {
            throw new CommandException("Could not initialize yentu. Your project has already been initialized with yentu.");
        }
        else if(!is_writable(dirname(Yentu::getPath(''))))
        {
            throw new CommandException("Your current directory is not writable.");
        }
        
        $params = $this->getParams($options);
        
        if(count($params) == 0 && defined('STDOUT'))
        {
            global $argv;
            throw new CommandException(
                "You didn't provide any parameters for initialization. Please execute "
                . "`{$argv[0]} init -i` to initialize yentu interractively. "
                . "You can also try `{$argv[0]} init --help` for more information."
            );
        }
        
        $this->createConfigFile($params); 
        Config::readPath(Yentu::getPath('config'), 'yentu');        
        $db = \yentu\DatabaseManipulator::create($params);
        
        if($db->getAssertor()->doesTableExist('yentu_history'))
        {
            throw new CommandException("Could not initialize yentu. Your database has already been initialized with yentu.");
        }
        
        $db->createHistory();
        $db->disconnect();
                
        ClearIce::output("Yentu successfully initialized.\n");
    }

    public function reverse()
    {
        
    }

}

