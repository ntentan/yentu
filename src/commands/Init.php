<?php
namespace yentu\commands;

use yentu\Command;
use yentu\Yentu;
use clearice\ClearIce;

/**
 * 
 */
class Init implements Command
{
    private function getParams($options)
    {
        if($options['interractive']) 
        {   
            $params['driver'] = ClearIce::getResponse('Database type', array(
                    'required' => true,
                    'answers' => array(
                        'postgresql'
                    )
                )
            );
            
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
        else
        {
            $params = $options;
        }
        return $params;
    }
    
    public function createConfigFile($params)
    {
        mkdir(Yentu::getPath(''));
        mkdir(Yentu::getPath('config'));
        mkdir(Yentu::getPath('migrations'));
        
        $configFile = new \yentu\CodeWriter();
        $configFile->add('$config = array(');
        $configFile->addIndent();
        $configFile->add("'driver' => '{$params['driver']}',");
        $configFile->add("'host' => '{$params['host']}',");
        $configFile->add("'port' => '{$params['port']}',");
        $configFile->add("'dbname' => '{$params['dbname']}',");
        $configFile->add("'user' => '{$params['user']}',");
        $configFile->add("'password' => '{$params['password']}',");
        $configFile->decreaseIndent();
        $configFile->add(');');
        
        file_put_contents(Yentu::getPath("config/default.php"), $configFile);        
    }
    
    public function run($options)
    {
        if(file_exists(Yentu::getPath('')))
        {
            throw new CommandError("Could not initialize yentu. Your project has already been initialized with yentu.");
        }
        else if(!is_writable(dirname(Yentu::getPath(''))))
        {
            throw new CommandError("Your current directory is not writable.");
        }
        
        $params = $this->getParams($options);
        
        if(count($params) == 0 && defined('STDOUT'))
        {
            global $argv;
            throw new CommandError(
                "Ooops! You didn't provide any parameters. Please execute "
                . "`{$argv[0]} init -i` to execute command interractively. "
                . "You can also try `{$argv[0]} init --help` for more information.\n"
            );
        }
        
        $db = \yentu\DatabaseDriver::getConnection($params);
        
        if($db->doesTableExist('yentu_history'))
        {
            throw new CommandError("Could not initialize yentu. Your database has already been initialized with yentu.");
        }
        
        $db->createHistory();
        $this->createConfigFile($params);
                
        Yentu::out("Yentu successfully initialized.\n");
    }
}

