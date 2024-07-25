<?php
namespace yentu;

class ChangeReverser
{
    private $driver;

    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

    public function call($method, $arguments) 
    {
        $reversedMethod = $this->reverseMethod($method);
        $reversedArguments = $this->reverseArguments($arguments[0]);
        return $this->driver->$reversedMethod($reversedArguments);
    }    
    
    private function reverseMethod($method)
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
    
    private function reverseArguments($arguments)
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
