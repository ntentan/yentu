<?php
namespace yentu;

class Parameters 
{   
    public static function wrap($parameters, $defaults = []) 
    {
        if(!is_array($parameters)) {
            return $parameters;
        } 
        
        foreach($defaults as $key => $value) {
            if(is_numeric($key) && !isset($parameters[$value])) {
                $parameters[$value] = null;
            } else if(!is_numeric($key) && !isset($parameters[$key])) {
                $parameters[$key] = $value;              
            }
        }        
        return $parameters;
    }
}
