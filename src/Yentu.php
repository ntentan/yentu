<?php
namespace yentu;

use clearice\ClearIce;

class Yentu
{
    private static $home = './yentu';
    private static $streamResource;

    public static function setDefaultHome($home)
    {
        self::$home = $home;
    }    
    
    public static function getDefaultHome()
    {
        return self::$home;
    }
    
    public static function getPath($path)
    {
        return self::$home . "/$path";
    }
    
    public static function getMigrations()
    {
        $migrationFiles = scandir(Yentu::getPath('migrations'), 0);        
        $migrations = array();
        foreach($migrationFiles as $migration)
        {
            $details = self::getMigrationDetails($migration);
            if($details === false) continue;
            $migrations[] = $details;
        }
        
        return $migrations;
    }
    
    private static function getMigrationDetails($migration)
    {
        if(preg_match("/^(?<timestamp>[0-9]{14})\_(?<migration>[a-z][a-z0-9\_]*)\.php$/", $migration, $details))
        {
            $details['file'] = $migration;
        }
        else
        {
            $details = false;            
        }
        return $details;
    }
    
    public static function toCamelCase($string)
    {
        $segments = explode('_', $string);
        $camel = '';
        foreach($segments as $segment)
        {
            $camel+=ucfirst($segment);
        }
        return $camel;
    }
    
    public static function announce($command, $itemType, $arguments)
    {
        ClearIce::output(
            "\n  - " . ucfirst("{$command}ing ") . 
            preg_replace("/([a-z])([A-Z])/", "$1 $2", $itemType) . " " .
            self::getDetails($command, $arguments),
            ClearIce::OUTPUT_LEVEL_2
        );
        ClearIce::output(".");
    }
    
    private static function getDetails($name, $arguments)
    {
        if($name == 'add')
        {
            $dir = 'to';
        }
        else if($name == 'drop')
        {
            $dir = 'from';
        }
        
        if(isset($arguments['table']) && isset($arguments['schema']))
        {
            $destination = "table '{$arguments['schema']}.{$arguments['table']}'";
        }
        elseif(isset($arguments['schema']) && !isset($arguments['table']))
        {
            $destination = "schema '{$arguments['schema']}'";
        }
        return is_string($arguments) ? " $arguments" : "'{$arguments["name"]}' $dir $destination";
    }    
}
