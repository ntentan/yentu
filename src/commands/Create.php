<?php
namespace yentu\commands;

use yentu\Yentu;

class Create implements \yentu\Command
{
    public function run($options)
    {
        $this->createFile($options['stand_alones'][0]);
    }
    
    public function createFile($name)
    {
        $timestamp = \yentu\Timestamp::get();
        if($name == '')
        {
            throw new CommandError(
                "Please provide a name for your new migration"
            );            
        }
        else if(preg_match("/[a-z][a-z0-9\_]*/",$name))
        {
            $code = new \yentu\CodeWriter();
            $path = Yentu::getPath("migrations/{$timestamp}_{$name}.php");
            file_put_contents($path, $code);
            Yentu::out("\nAdded $path for new migration\n\n");
        }
        else
        {
            throw new \Exception(
                "Migration names must always start with a lowercase alphabet and "
                . "can only consist of lower case alphabets, numbers and underscores." 
            );
        }
    }
}