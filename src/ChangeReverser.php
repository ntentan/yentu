<?php
namespace yentu;

class ChangeReverser
{
    private static $driver;

    public static function setDriver($driver)
    {
        self::$driver = $driver;
    }


    public static function call($method, $arguments) 
    {
        $reflection = new \ReflectionMethod(self::$driver, self::reverse($method));
        return $reflection->invokeArgs(self::$driver, $arguments);        
    }    
    
    private static function reverse($method)
    {
        return preg_replace_callback(
            "/^(?<action>add|drop)/", 
            function($matches){
                switch($matches['action'])
                {
                    case 'add': return 'drop';
                    case 'drop': return 'add';
                }
            }, $method
        );
    }
}
