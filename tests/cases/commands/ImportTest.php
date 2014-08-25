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

require "vendor/autoload.php";

use org\bovigo\vfs\vfsStream;

class ImportTest extends \yentu\tests\YentuTest
{
    public function setup()
    {
        $this->pdo = new \PDO($GLOBALS['IMPORT_DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWORD']);  
        $this->pdo->query("DROP TABLE IF EXISTS yentu_history CASCADE");         
        $this->pdo->query("DROP SEQUENCE IF EXISTS yentu_history_id_seq CASCADE");         
        $init = new \yentu\commands\Init();
        vfsStream::setup('home');
        yentu\Yentu::setDefaultHome(vfsStream::url('home/yentu'));
        yentu\Yentu::setStreamUrl(vfsStream::url('home/output.txt'));
        $init->run(
            array(
                'driver' => 'postgresql',
                'host' => $GLOBALS['DB_HOST'],
                'dbname' => $GLOBALS['IMPORT_DB_NAME'],
                'user' => $GLOBALS['DB_USER'],
                'password' => $GLOBALS['DB_PASSWORD']
            )
        ); 
    }
    
    public function testImport()
    {
        $codeWriter = $this->getMock('\\yentu\\CodeWriter', array('getTimestamp'));
        $codeWriter->method('getTimestamp')->willReturn('25th August, 2014 14:30:13');
        
        $import = new yentu\commands\Import();
        $import->setCodeWriter($codeWriter);
        $timestamp = $import->run(array());
        $this->assertFileExists(
            vfsStream::url("home/yentu/migrations/{$timestamp}_import.php")
        );
        $this->assertStringEqualsFile(
            vfsStream::url("home/yentu/migrations/{$timestamp}_import.php"),
            file_get_contents('tests/expected/target_import.php')
        );
    }
    
    public function tearDown()
    {
        $this->deinitialize();
    }
}
