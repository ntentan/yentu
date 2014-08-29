<?php

/* 
 * The MIT License
 *
 * Copyright 2014 Ekow Abaka Ainoson
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

use org\bovigo\vfs\vfsStream;

error_reporting(E_ALL ^ E_NOTICE);

class YentuTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \PDO
     */
    protected $pdo;
    protected $clearHistory = true;
    
    public function assertTableExists($table, $message = '')
    {
        $constraint = new constraints\TableExists();
        $constraint->setPDO($this->pdo);
        $this->assertThat($table, $constraint, $message);
    }
    
    public function assertColumnExists($column, $table, $message = '')
    {
        $constraint = new constraints\ColumnExists();
        $constraint->setPDO($this->pdo); 
        $constraint->setTable($table);
        $this->assertThat($column, $constraint, $message);
    }
    
    public function assertColumnNullable($column, $table, $message = '')
    {
        $constraint = new constraints\ColumnNullability();
        $constraint->setPDO($this->pdo);
        $constraint->setTable($table);
        $this->assertThat($column, $constraint, $message);
    }
    
    public function assertColumnNotNullable($column, $table, $message = '')
    {
        $constraint = new constraints\ColumnNullability();
        $constraint->setPDO($this->pdo);
        $constraint->setTable($table);
        $constraint->setNullability('NO');
        $this->assertThat($column, $constraint, $message);
    }    
    
    protected function initialize($dsn = '')
    {
        $this->pdo = new \PDO($GLOBALS["{$dsn}_DB_DSN"], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWORD']);  
        
        if($this->clearHistory === true)
        {
            $this->pdo->query("DROP TABLE IF EXISTS yentu_history CASCADE"); 
            $this->pdo->query("DROP SEQUENCE IF EXISTS yentu_history_id_seq"); 
        }
        
        $init = new \yentu\commands\Init();
        vfsStream::setup('home');
        \yentu\Yentu::setDefaultHome(vfsStream::url('home/yentu'));
        \yentu\Yentu::setOutputStreamUrl(vfsStream::url('home/output.txt'));
        $init->run(
            array(
                'driver' => 'postgresql',
                'host' => $GLOBALS['DB_HOST'],
                'dbname' => $GLOBALS["{$dsn}_DB_NAME"],
                'user' => $GLOBALS['DB_USER'],
                'password' => $GLOBALS['DB_PASSWORD']
            )
        );            
    }
    
    protected function deinitialize()
    {
        
    }
}