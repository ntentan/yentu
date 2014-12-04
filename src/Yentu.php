<?php
namespace yentu;

define('YENTU_VERSION', '0.1.0-alpha');

use clearice\ClearIce;

class Yentu
{
    private static $home = './yentu';
    public static $version = YENTU_VERSION;

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
    
    public static function getMigrationPathsInfo()
    {
        require Yentu::getPath("config/default.php");
        return array_merge(array(
                array(
                'home' => Yentu::getPath('migrations')
                )
            ),
            is_array($other_migrations) ? $other_migrations : array()
        );
    }
    
    public static function getRunMirations()
    {
        $db = DatabaseManipulator::create();
        $runMigrations = $db->query("SELECT DISTINCT version, migration, default_schema FROM yentu_history ORDER BY version");
        $migrations = array();
        foreach($runMigrations as $migration)
        {
            $migrations[$migration['version']] = array(
                'timestamp' => $migration['version'],
                'migration' => $migration['migration'],
                'default_schema' => $migration['default_schema']
            );
        }
        
        return $migrations;
    }
    
    public static function getMigrations($path)
    {
        $migrationFiles = scandir($path, 0);        
        $migrations = array();
        foreach($migrationFiles as $migration)
        {
            $details = self::getMigrationDetails($migration);
            if($details === false) continue;
            $migrations[$details['timestamp']] = $details;
            unset($migrations[$details['timestamp']][0]);
            unset($migrations[$details['timestamp']][1]);
            unset($migrations[$details['timestamp']][2]);
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
    
    public static function greet()
    {
        $version = Yentu::$version;
$welcome = <<<WELCOME
Yentu Database Migration Tool
Version $version


WELCOME;
        ClearIce::output($welcome);        
    }
}
