<?php
namespace yentu\commands;

class Init implements \yentu\Command
{
    private function getParams($options)
    {
        if($options['interractive']) 
        {
            $cli = new \ClearIce();
        }
        else
        {
            $params = $options;
        }
        return $params;
    }
    
    public function run($options)
    {
        var_dump($options);
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
    }
}