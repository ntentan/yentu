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
use clearice\ClearIce;
use ntentan\config\Config;

error_reporting(E_ALL);

class YentuTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \PDO
     */
    protected $pdo;
    protected $testDatabase;
    protected $testDefaultSchema;
    
    public function setup()
    {
        require_once "src/globals.php";
        
        ClearIce::setOutputLevel(ClearIce::OUTPUT_LEVEL_1);  
        
        $GLOBALS['DRIVER'] = getenv('YENTU_DRIVER');
        $GLOBALS['DB_DSN'] = getenv('YENTU_BASE_DSN');
        
        if(getenv('YENTU_FILE') === false)
        {
            $GLOBALS['DB_FULL_DSN'] = "{$GLOBALS['DB_DSN']};dbname={$this->testDatabase}"; 
            $GLOBALS['DB_NAME'] = $this->testDatabase;
            $GLOBALS['DEFAULT_SCHEMA'] = (string)getenv('YENTU_DEFAULT_SCHEMA') == '' ? 
                $this->testDatabase : (string)getenv('YENTU_DEFAULT_SCHEMA');
            $GLOBALS['DB_FILE'] = '';
        }
        else
        {
            $GLOBALS['DB_FULL_DSN'] = $GLOBALS['DB_DSN'];
            $GLOBALS['DB_FILE'] = getenv('YENTU_FILE');
            $GLOBALS['DB_NAME'] = '';
            $GLOBALS['DEFAULT_SCHEMA'] = '';
        }
        
        $GLOBALS['DB_USER'] = (string)getenv('YENTU_USER');
        $GLOBALS['DB_PASSWORD'] = (string)getenv('YENTU_PASSWORD');
        $GLOBALS['DB_HOST'] = (string)getenv('YENTU_HOST');
        
        $timer = $this->getMockBuilder("\\yentu\\Timer")->setMethods(array('stopInstance', 'startInstance'))->getMock();
        $timer->method('stopInstance')->willReturn(10.0000);
        \yentu\Timer::setInstance($timer);        
    }
    
    public function tearDown()
    {
        $this->pdo = null;
        Config::reset();
    }
    
    public function assertSchemaExists($schema, $message = '')
    {
        $constraint = new constraints\SchemaExists();
        $constraint->setPDO($this->pdo);
        $this->assertThat($schema, $constraint, $message);
    }
    
    public function assertTableExists($table, $message = '')
    {
        $constraint = new constraints\TableExists();
        $constraint->setPDO($this->pdo);
        $this->assertThat($table, $constraint, $message);
    }
    
    public function assertTableDoesntExist($table, $message = '')
    {
        $constraint = new constraints\TableExists();
        $constraint->negate();
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
        $constraint->setNullability(false);
        $this->assertThat($column, $constraint, $message);
    }  
    
    public function assertForeignKeyExists($table, $message = '')
    {
        $constraint = new constraints\ForeignKeyExists();
        $constraint->setPDO($this->pdo);
        $this->assertThat($table, $constraint, $message);
    }    
    
    public function assertForeignKeyDoesntExist($table, $message = '')
    {
        $constraint = new constraints\ForeignKeyExists();
        $constraint->negate();
        $constraint->setPDO($this->pdo);
        $this->assertThat($table, $constraint, $message);
    }      
    
    protected function createDb($name)
    {
        if(getenv('YENTU_FILE') !== false) 
        {
            if(file_exists(getenv('YENTU_FILE')))
            {
                unlink(getenv('YENTU_FILE'));
            }
        }
        else
        {
            $pdo = new \PDO($GLOBALS["DB_DSN"], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWORD']);  
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);     
            $pdo->exec("DROP DATABASE IF EXISTS $name");
            $pdo->exec("CREATE DATABASE $name"); 
            $pdo = null;        
        }
    }
    
    protected function initDb($dsn, $queries)
    {
        $pdo = new \PDO($dsn, $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWORD']);  
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);     
        $pdo->exec($queries);
        $pdo = null;         
    }
    
    protected function setupStreams()
    {
        vfsStream::setup('home');
        \yentu\Yentu::setDefaultHome(vfsStream::url('home/yentu'));
        \clearice\ClearIce::setStreamUrl('output', vfsStream::url('home/output.txt'));
    }
    
    protected function initYentu($name)
    {
        $init = new \yentu\commands\Init();
        $this->setupStreams();
        $init->run(
            array(
                'driver' => $GLOBALS['DRIVER'],
                'host' => $GLOBALS['DB_HOST'],
                'dbname' => $name,
                'user' => $GLOBALS['DB_USER'],
                'password' => $GLOBALS['DB_PASSWORD'],
                'file' => $GLOBALS['DB_FILE']
            )
        );
    }
    
    protected function connect($dsn)
    {
        $this->pdo = new \PDO($dsn, $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWORD']);  
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);        
    }
    
    protected function setupForMigration()
    {
        $this->createDb($GLOBALS['DB_NAME']);
        $this->connect($GLOBALS["DB_FULL_DSN"]);
        $this->initYentu($GLOBALS['DB_NAME']);
    }
    
    protected function skipSchemaTests()
    {
        if(getenv('YENTU_SKIP_SCHEMAS') === 'yes')
        {
            $this->markTestSkipped();
        }        
    }
}
