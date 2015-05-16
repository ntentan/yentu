<?php

/*
 * The MIT License
 *
 * Copyright 2015 ekow.
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

namespace yentu\manipulators;

/**
 * Description of Sqlite
 *
 * @author ekow
 */
class Sqlite extends \yentu\DatabaseManipulator
{
    private $placeholders = [];
    
    protected function _addAutoPrimaryKey($details) 
    {
        
    }
    
    private function getColumnDef($details)
    {
        return sprintf(
            "%s %s %s", $details['name'], 
            $this->convertTypes(
                $details['type'], 
                self::CONVERT_TO_DRIVER, 
                $details['length']
            ),
            $details['nulls'] === false ? 'NOT NULL' : ''
        );
    }

    protected function _addColumn($details) 
    {
        if($this->placeholders[$details['table']])
        {
            $this->query("DROP TABLE {$details['table']}");
            $this->query(sprintf("CREATE TABLE %s (%s)",
                    $details['table'],
                    $this->getColumnDef($details)
                )
            );
            unset($this->placeholders[$details['table']]);
        }
        else
        {
            $this->query("ALTER TABLE {$details['table']} ADD COLUMN " . $this->getColumnDef($details));
        }
    }

    protected function _addForeignKey($details) {
        
    }

    protected function _addIndex($details) {
        
    }

    protected function _addPrimaryKey($details) {
        
    }

    protected function _addSchema($name) {
        
    }

    protected function _addTable($details) 
    {
        $this->query("CREATE TABLE {$details['name']} (__yentu_placeholder_col INTEGER)");
        $this->placeholders[$details['name']] = true;
    }

    protected function _addUniqueKey($details) {
        
    }

    protected function _addView($details) {
        
    }

    protected function _changeColumnDefault($details) {
        
    }

    protected function _changeColumnName($details) {
        
    }

    protected function _changeColumnNulls($details) {
        
    }

    protected function _changeViewDefinition($details) {
        
    }

    protected function _dropAutoPrimaryKey($details) {
        
    }

    protected function _dropColumn($details) {
        
    }

    protected function _dropForeignKey($details) {
        
    }

    protected function _dropIndex($details) {
        
    }

    protected function _dropPrimaryKey($details) {
        
    }

    protected function _dropSchema($name) {
        
    }

    protected function _dropTable($details) {
        
    }

    protected function _dropUniqueKey($details) {
        
    }

    protected function _dropView($details) {
        
    }

    public function convertTypes($type, $direction, $length) 
    {
        $destinationType = null;
        $types = [
            ['integer', 'integer'],
            ['text', 'string'],
            ['real', 'double'],
            ['text', 'timestamp'],
            ['text', 'text'],
            ['blob', 'blob'],
            ['integer', 'boolean'],
            ['integer', 'bigint']
        ];
        
        $type = strtolower($type);
        
        foreach($types as $testType)
        {
            if($direction === self::CONVERT_TO_DRIVER && $testType[1] === $type)
            {
                $destinationType = $testType[0];
                break;
            }
            else if($direction === self::CONVERT_TO_YENTU && $testType[0] === $type)
            {
                $destinationType = $testType[1];
                break;
            }
        }
        
        if($destinationType == '')
        {
            throw new \yentu\DatabaseManipulatorException("Invalid data type {$type} requested"); 
        }
        else if($destinationType == 'text')
        {
            $destinationType .= $length === null ? '' : "($length)";
        }
        return $destinationType;              
    }
}
