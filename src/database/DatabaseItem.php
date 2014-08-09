<?php

namespace yentu\database;

class DatabaseItem 
{
    private $encapsulated;
    
    protected function setEncapsulated($encapsulated)
    {
        $this->encapsulated = $encapsulated;
    }
    
    public function __call($method, $arguments)
    {
        if(!is_object($this->encapsulated))
        {
            throw new \Exception("Failed to call method {$method}");
        }
        else if (method_exists($this->encapsulated, $method))
        {
            $method = new \ReflectionMethod($this->encapsulated, $method);
            return $method->invokeArgs($this->encapsulated, $arguments);
        }
        else
        {
            return $this->encapsulated->__call($method, $arguments);
        }
    }
}

