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
        $method = self::reverseMethod($method);
        $arguments = Parameters::wrap(self::reverseArguments($arguments[0]));
        return self::$driver->$method($arguments);
    }    
    
    private static function reverseMethod($method)
    {
        return preg_replace_callback(
            "/^(?<action>add|drop|reverse|execute)/", 
            function($matches){
                switch($matches['action'])
                {
                    case 'add': return 'drop';
                    case 'drop': return 'add';
                    case 'reverse': return 'execute';
                    case 'execute': return 'reverse';
                }
            }, $method
        );
    }
    
    private static function reverseArguments($arguments)
    {
        if(isset($arguments['from']) && isset($arguments['to']))
        {
            return array(
                'to' => $arguments['from'],
                'from' => $arguments['to']
            );
        }
        else
        {
            return $arguments;
        }
    }
}
