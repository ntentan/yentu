<?php
namespace yentu;

class Yentu
{
    // Total silence. Like mute :-X
    const OUTPUT_LEVEL_0 = 0;
    
    // General top level info.
    const OUTPUT_LEVEL_1 = 1;
    
    // Details of operations being performed.
    const OUTPUT_LEVEL_2 = 2;
    
    // Log every single query too.
    const OUTPUT_LEVEL_3 = 3;
    
    private static $home = './yentu';
    private static $level = Yentu::OUTPUT_LEVEL_1;
    private static $streamResource;


    public static function setDefaultHome($home)
    {
        self::$home = $home;
    }    
    
    public static function getPath($path)
    {
        return self::$home . "/$path";
    }
    
    public static function getMigrations()
    {
        return scandir(Yentu::getPath('migrations'), 0);        
    }
    
    public static function setOutputStreamUrl($url)
    {
        self::$streamResource = fopen($url, 'w');
    }

    /**
     * 
     * @param type $string
     */
    public static function out($string, $level = Yentu::OUTPUT_LEVEL_1)
    {
        if(!is_resource(self::$streamResource))
        {
            self::$streamResource = fopen('php://stdout', 'w');
        }
        if($level <= self::$level)
        {
            fputs(self::$streamResource, $string);
            fflush(self::$streamResource);
        }
    }
    
    public static function setOutputLevel($level)
    {
        self::$level = $level;
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
    
    public static function announce($name, $arguments)
    {
        preg_match("/(?<command>add|drop|chang(e))(?<item_type>[a-zA-Z]+)/", $name, $matches);
        Yentu::out(
            ucfirst("{$matches['command']}ing ") . 
            preg_replace("/([a-z])([A-Z])/", "$1 $2", $matches['item_type']) . " " .
            self::getDetails($matches['command'], $arguments) ."\n",
            self::OUTPUT_LEVEL_2
        );
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

