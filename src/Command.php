<?php
namespace yentu;

abstract class Command
{
    private static $home = './yentu';

    abstract public function run($options);
    
    public static function setDefaultHome($home)
    {
        self::$home = $home;
    }    
    
    public static function getPath($path)
    {
        return self::$home . "/$path";
    }
}

