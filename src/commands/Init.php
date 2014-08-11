<?php
namespace yentu\commands;

class Init implements \yentu\Command
{
    private function getParams($options)
    {
        if($options['interractive']) 
        {
            $cli = new \ClearIce();
            
            $params['driver'] = $cli->getResponse('Database type', array(
                    'required' => true,
                    'answers' => array(
                        'postgresql'
                    )
                )
            );
            
            $params['host'] = $cli->getResponse('Database host', array(
                    'required' => true,
                    'default' => 'localhost'
                )
            );
            
            $params['port'] = $cli->getResponse('Database port');
            
            $params['dbname'] = $cli->getResponse('Database name', array(
                    'required' => true
                )
            );
            
            $params['user'] = $cli->getResponse('Database user name', array(
                    'required' => true
                )
            );            
            
            $params['password'] = $cli->getResponse('Database password', array(
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
    
    public function run($options)
    {
        if(file_exists('yentu'))
        {
            throw new \Exception("Your project has already been initialized with yentu.");
        }
        else if(!is_writable('./'))
        {
            throw new \Exception("Your current directory is not writable.");
        }
        
        $params = $this->getParams($options);
        
        if(count($params) == 0 && defined('STDOUT'))
        {
            global $argv;
            throw new \Exception(
                wordwrap(
                    "Ooops! You didn't provide any parameters. \nPlease execute "
                    . "`{$argv[0]} init -i` to execute command interractively. "
                    . "You can also try `{$argv[0]} init --help` for more information.\n"
                )
            );
        }
        
        $db = \yentu\DatabaseDriver::getConnection($params);
        
        if($db->doesTableExist('yentu_version'))
        {
            throw new \Exception("Your database has already been initialized with yentu.");
        }
        
        $db->addTable(
            array(
                'name' => 'yentu_version'
            )
        );
        
        $db->addColumn(
            array(
                'table' => 'yentu_version',
                'name' => 'version',
                'type' => 'string'
            )
        );
        
        mkdir('yentu');
        mkdir('yentu/config');
        mkdir('yentu/migrations');
        
        $configFile = new \yentu\CodeWriter();
        $configFile->add('$config = array(');
        $configFile->addIndent();
        $configFile->add("'driver' => '{$params['driver']}'");
        $configFile->add("'host' => '{$params['host']}'");
        $configFile->add("'port' => '{$params['port']}'");
        $configFile->add("'dbname' => '{$params['dbname']}'");
        $configFile->add("'user' => '{$params['user']}'");
        $configFile->add("'password' => '{$params['password']}'");
        $configFile->decreaseIndent();
        $configFile->add(');');
        
        file_put_contents('yentu/config/default.php', $configFile);
    }
}