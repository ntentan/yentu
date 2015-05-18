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

namespace yentu\tests;

abstract class YentuConstraint extends \PHPUnit_Framework_Constraint
{
    /**
     *
     * @var \PDO
     */
    protected $pdo;
    private $negate;
    
    /**
     *
     * @var \yentu\tests\SchemaInfo
     */
    protected $schemaInfo;
    
    public function negate()
    {
        $this->negate = true;
    }
    
    protected function processResult($result)
    {
        if($this->negate === true) return !$result; else return $result;
    }
    
    public function setPDO($pdo)
    {
        $this->pdo = $pdo;
        $schemaInfoClass = "\\yentu\\tests\\schema_info\\" . ucfirst(getenv('YENTU_DRIVER')) . "Info";
        $this->schemaInfo = new $schemaInfoClass();
    }
    
    public function setTable($table)
    {
        if(is_string($table))
        {
            $table = array(
                'table' => $table,
                'schema' => $GLOBALS['DEFAULT_SCHEMA']
            );
        }
        else
        {
            $table = $table;
        }
        $this->schemaInfo->setTable($table);
    }
}