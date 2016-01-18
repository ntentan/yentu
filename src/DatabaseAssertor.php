<?php
/* 
 * The MIT License
 *
 * Copyright 2014 ekow.
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

class DatabaseAssertor
{
    private $description;
    
    public function __construct($description)
    {        
        $this->description = $description;
    }
    
    public function doesSchemaExist($details)
    {
        return isset($this->description['schemata'][$details]);
    }
    
    public function doesTableExist($details)
    {
        if(is_string($details))
        {
            $details = array(
                'schema' => false,
                'name' => $details
            );
        }
        
        return $details['schema'] == false? 
            isset($this->description['tables'][$details['name']])  : 
            isset($this->description['schemata'][$details['schema']]['tables'][$details['name']]);
    }
    
    public function doesColumnExist($details)
    {
        $table = $this->getTableDetails($details['schema'], $details['table']);
        return isset($table['columns'][$details['name']]) ? 
            Parameters::wrap($table['columns'][$details['name']]) : false;
    }
    
    private function doesItemExist($details, $type)
    {
        $table = $this->getTableDetails($details['schema'], $details['table']);
        if(isset($details['columns']))
        {
            return isset($table["flat_$type"][$details['columns'][0]]) ? $table["flat_$type"][$details['columns'][0]] : false;
        }
        else if(isset($details['name']))
        {
            return isset($table[$type][$details['name']]) ? $table[$type][$details['name']] : false;
        }        
    }
    
    public function doesForeignKeyExist($details)
    {
        return $this->doesItemExist($details, 'foreign_keys');
    }
    
    public function doesUniqueKeyExist($details)
    {
        return $this->doesItemExist($details, 'unique_keys');
    }
    
    public function doesPrimaryKeyExist($details)
    {
        return $this->doesItemExist($details, 'primary_key');
    } 
    
    public function doesIndexExist($details)
    {
        return $this->doesItemExist($details, 'indices');
    }
    
    public function doesViewExist($details)
    {
        if(is_string($details))
        {
            $details = array(
                'schema' => false,
                'name' => $details
            );
        }
        
        // too complex 
        if($details['schema'] == false) {
            return isset($this->description['views'][$details['name']]) ? $this->description['views'][$details['name']]['definition'] : false ;
        } else {
            return (isset($this->description['schemata'][$details['schema']]['views'][$details['name']]) ?
                $this->description['schemata'][$details['schema']]['views'][$details['name']]['definition'] : false);
        }
    }
    
    private function getTableDetails($schema, $table)
    {
        return $schema === false ? $this->description['tables'][$table] : 
            $this->description['schemata'][$schema]['tables'][$table];        
    }    
}
