<?php
namespace yentu\commands;

class Create extends \yentu\Command
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
            throw new \Exception(
                "Please provide a name for your new migration"
            );            
        }
        else if(preg_match("/[a-z][a-z0-9\_]*/",$name))
        {
            $code = new \yentu\CodeWriter();
            file_put_contents("yentu/migrations/{$timestamp}_{$name}.php", $code);
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