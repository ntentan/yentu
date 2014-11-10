<?php
namespace yentu\commands;

use yentu\Yentu;

class Create implements \yentu\Command
{
    public function run($options)
    {
        Yentu::greet();
        $this->createFile($options['stand_alones'][0]);
    }
    
    private function checkExisting($name)
    {
        if(count(glob(Yentu::getPath("migrations/*_{$name}.php"))) > 0)
        {
            throw new CommandError("A migration already exists with the name {$name}");
        }
    }
    
    private function checkName($name)
    {
        if($name == '')
        {
            throw new CommandError(
                "Please provide a name for your new migration"
            );            
        }        
        else if(!preg_match("/[a-z][a-z0-9\_]*/", $name))
        {
            throw new CommandError(
                "Migration names must always start with a lowercase alphabet and "
                . "can only consist of lower case alphabets, numbers and underscores." 
            );            
        }
    }
    
    public function createFile($name)
    {
        $this->checkExisting($name);
        $this->checkName($name);
        
        $timestamp = \yentu\Timestamp::get();
        $code = new \yentu\CodeWriter();
        $path = Yentu::getPath("migrations/{$timestamp}_{$name}.php");
        file_put_contents($path, $code);
        \clearice\ClearIce::output("\nAdded $path for new migration.\n");
    }
}
