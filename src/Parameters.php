<?php

/* 
 * The MIT License
 *
 * Copyright 2016 ekow.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace yentu;

class Parameters implements \ArrayAccess
{
    private $parameters;
    
    private function __construct($parameters, $defaults)
    {
        $this->parameters = $parameters;
        foreach($defaults as $key => $value) {
            if(!isset($this->parameters[$key])) {
                $this->parameters[$key] = $value;
            }
        }
    }
    
    public static function wrap($parameters, $defaults = []) 
    {
        return new Parameters($parameters, $defaults);
    }
    
    public function get($key, $default = null)
    {
        $return = isset($this->parameters[$key]) ? $this->parameters[$key] : $default;
        if(is_array($return)) {
            $return = Parameters::wrap($return);
            $this->parameters[$key] = $return;
        } 
        return $return;
    }
    
    public function getArray()
    {
        return $this->parameters;
    }

    public function offsetExists($key)
    {
        return isset($this->parameters[$key]);
    }

    public function offsetGet($key)
    {
        return $this->get($key);
    }

    public function offsetSet($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    public function offsetUnset($key)
    {
        unset($this->parameters[$key]);
    }
}
