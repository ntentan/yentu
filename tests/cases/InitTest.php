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

namespace yentu\tests\cases;

use \org\bovigo\vfs\vfsStream;
use clearice\ClearIce;

class InitTest extends \yentu\tests\YentuTest
{
    public function setUp()
    {
        $this->testDatabase = 'yentu_tests';        
        parent::setup();
        $this->createDb($GLOBALS['DB_NAME']);
        $this->connect($GLOBALS['DB_FULL_DSN']);
        $this->setupStreams();
    }  
    
    public function testParameters()
    {
        $initCommand = new \yentu\commands\Init();
        
        ob_start();
        $initCommand->run(
            array(
                'driver' => $GLOBALS['DRIVER'],
                'host' => $GLOBALS['DB_HOST'],
                'dbname' => $GLOBALS['DB_NAME'],
                'user' => $GLOBALS['DB_USER'],
                'password' => $GLOBALS['DB_PASSWORD'],
                'file' => $GLOBALS['DB_FILE']
            )
        );
        $this->runAssertions();
    }
    
    private function runAssertions()
    {
        $output = ob_get_clean();
        $this->assertEquals(true, file_exists(vfsStream::url("home/yentu")));
        $this->assertEquals(true, is_dir(vfsStream::url("home/yentu")));
        $this->assertEquals(true, file_exists(vfsStream::url("home/yentu/config")));
        $this->assertEquals(true, is_dir(vfsStream::url("home/yentu/config")));
        $this->assertEquals(true, file_exists(vfsStream::url("home/yentu/migrations")));
        $this->assertEquals(true, is_dir(vfsStream::url("home/yentu/migrations")));
        $this->assertEquals(true, is_file(vfsStream::url('home/yentu/config/default.conf.php')));
        
        // provides $config below
        $config = require(vfsStream::url('home/yentu/config/default.conf.php'));
        
        $this->assertEquals(array(
            'driver' => $GLOBALS['DRIVER'],
            'host' => $GLOBALS['DB_HOST'],
            'port' => '',
            'dbname' => $GLOBALS['DB_NAME'],
            'user' => $GLOBALS['DB_USER'],
            'password' => $GLOBALS['DB_PASSWORD'],
            'file' => $GLOBALS['DB_FILE']
        ), $config['db']);      
        
        $this->assertTableExists('yentu_history');
        $this->assertColumnExists('session', 'yentu_history');        
        $this->assertColumnExists('version', 'yentu_history');        
        $this->assertColumnExists('method', 'yentu_history');        
        $this->assertColumnExists('arguments', 'yentu_history');        
        $this->assertColumnExists('migration', 'yentu_history');        
        $this->assertColumnExists('id', 'yentu_history');        
    }
    
    public function testInterractive()
    {
        ClearIce::setStreamUrl('error', vfsStream::url("home/error.out"));
        ClearIce::setStreamUrl('output', vfsStream::url("home/standard.out"));
        ClearIce::setStreamUrl('input', vfsStream::url("home/responses.in"));
        
        // Write the input for the interractive test
        if(getenv('YENTU_HOST') === false)
        {
            file_put_contents(vfsStream::url("home/responses.in"),
                "{$GLOBALS['DRIVER']}\n"
                . "{$GLOBALS['DB_FILE']}\n"
            );            
        }
        else
        {
            file_put_contents(vfsStream::url("home/responses.in"),
                "{$GLOBALS['DRIVER']}\n"
                . "{$GLOBALS['DB_HOST']}\n"
                . "\n"
                . "{$GLOBALS['DB_NAME']}\n"
                . "{$GLOBALS['DB_USER']}\n"
                . "{$GLOBALS['DB_PASSWORD']}\n"
            );
        }
        
        $initCommand = new \yentu\commands\Init;
        \yentu\Yentu::setDefaultHome(vfsStream::url('home/yentu'));
        ob_start();
        $initCommand->run(array(
            'interractive' => true
        ));
        $this->runAssertions();
    }
    
    /**
     * @expectedException \yentu\exceptions\CommandException
     */
    public function testUnwritable()
    {
        vfsStream::setup('home', 0444);
        $initCommand = new \yentu\commands\Init();
        \yentu\Yentu::setDefaultHome(vfsStream::url("home/yentu"));
        ClearIce::setOutputLevel(ClearIce::OUTPUT_LEVEL_0); 
        $initCommand->run(
            array(
                'driver' => 'postgresql',
                'host' => $GLOBALS['DB_HOST'],
                'dbname' => $GLOBALS['DB_NAME'],
                'user' => $GLOBALS['DB_USER'],
                'password' => $GLOBALS['DB_PASSWORD']
            )
        );
    }
    
    /**
     * @expectedException \yentu\exceptions\CommandException
     */    
    public function testExistingDir()
    {
        mkdir(vfsStream::url('home/yentu'));
        $initCommand = new \yentu\commands\Init();
        \yentu\Yentu::setDefaultHome(vfsStream::url("home/yentu"));
        $initCommand->run(
            array(
                'driver' => 'postgresql',
                'host' => $GLOBALS['DB_HOST'],
                'dbname' => $GLOBALS['DB_NAME'],
                'user' => $GLOBALS['DB_USER'],
                'password' => $GLOBALS['DB_PASSWORD']
            )
        );        
    }
    
    /**
     * @expectedException \yentu\exceptions\CommandException
     */     
    public function testNoParams()
    {
        $initCommand = new \yentu\commands\Init();
        \yentu\Yentu::setDefaultHome(vfsStream::url("home/yentu"));
        $initCommand->run(array());         
    }
    
    /**
     * @expectedException \yentu\exceptions\CommandException
     */    
    public function testExistingDb()
    {
        $this->pdo->query('CREATE TABLE yentu_history(dummy INTEGER)');
        $initCommand = new \yentu\commands\Init();
        \yentu\Yentu::setDefaultHome(vfsStream::url("home/yentu"));
        $initCommand->run(
            array(
                'driver' => $GLOBALS['DRIVER'],
                'host' => $GLOBALS['DB_HOST'],
                'dbname' => $GLOBALS['DB_NAME'],
                'user' => $GLOBALS['DB_USER'],
                'password' => $GLOBALS['DB_PASSWORD'],
                'file' => $GLOBALS['DB_FILE']
            )
        );            
    }
}
