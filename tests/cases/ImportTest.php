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

use org\bovigo\vfs\vfsStream;

class ImportTest extends \yentu\tests\YentuTest
{
    public function setUp()
    {
        $this->testDatabase = 'yentu_import_test';
        parent::setup();
        $this->createDb($GLOBALS['DB_NAME']);
        $this->initYentu($GLOBALS['DB_NAME']);
    }
    
    public function testImport()
    {
        $this->initDb($GLOBALS['DB_FULL_DSN'], file_get_contents("tests/sql/{$GLOBALS['DRIVER']}/system.sql"));
        $codeWriter = $this->getMock('\\yentu\\CodeWriter', array('getTimestamp'));
        $codeWriter->method('getTimestamp')->willReturn('25th August, 2014 14:30:13');
        
        $import = new \yentu\commands\Import();
        $import->setCodeWriter($codeWriter);
        $description = $import->run(array());
        $newVersion = $import->getNewVersion();
        $this->assertFileExists(
            vfsStream::url("home/yentu/migrations/{$newVersion}_import.php")
        );
        
        require "tests/expected/{$GLOBALS['DRIVER']}/import.php";
        $descriptionArray = $description->getArray();
        
        unset($descriptionArray['tables']['yentu_history']);
        $this->assertEquals(
            $expectedDescription, 
            [
                'schemata' => $descriptionArray['schemata'],
                'tables' => $descriptionArray['tables'],
                'views' => $descriptionArray['views']
            ]);
    }
    
    public function testSchemaImport()
    {
        $this->skipSchemaTests();
        $this->connect($GLOBALS['DB_FULL_DSN']);
        $this->pdo->query('DROP SCHEMA IF EXISTS hr');
        $this->pdo->query('DROP SCHEMA IF EXISTS common');
        
        $this->initDb($GLOBALS['DB_FULL_DSN'], file_get_contents("tests/sql/{$GLOBALS['DRIVER']}/import_schema.sql"));
        $this->connect($GLOBALS['DB_FULL_DSN']);
        
        $codeWriter = $this->getMock('\\yentu\\CodeWriter', array('getTimestamp'));
        $codeWriter->method('getTimestamp')->willReturn('25th August, 2014 14:30:13');
        $import = new \yentu\commands\Import();
        $import->setCodeWriter($codeWriter);
        $import->run(array());
        $newVersion = $import->getNewVersion();
        $this->assertFileExists(
            vfsStream::url("home/yentu/migrations/{$newVersion}_import.php")
        );   
        $this->assertSchemaExists('common');
        $this->assertSchemaExists('hr');
    }
    
    public function testViewImport()
    {
        $this->initDb($GLOBALS['DB_FULL_DSN'], file_get_contents("tests/sql/{$GLOBALS['DRIVER']}/import_views.sql"));
        $this->connect($GLOBALS['DB_FULL_DSN']);
        
        $codeWriter = $this->getMock('\\yentu\\CodeWriter', array('getTimestamp'));
        $codeWriter->method('getTimestamp')->willReturn('25th August, 2014 14:30:13');
        $import = new \yentu\commands\Import();
        $import->setCodeWriter($codeWriter);
        $import->run(array());
        $newVersion = $import->getNewVersion();
        $this->assertFileExists(
            vfsStream::url("home/yentu/migrations/{$newVersion}_import.php")
        );   
            
        $this->assertTableExists('employees_view');
        $this->assertTableExists('disabled_employees_view');
    }    
        
    /**
     * @expectedException \yentu\exceptions\CommandException
     */
    public function testImportNonEmptyMigrations()
    {
        file_put_contents(vfsStream::url('home/yentu/migrations/1234568901234_existing.php'), 'nothing');
        $import = new \yentu\commands\Import();
        $import->run(array());        
    }
    
    public function testDatabaseNotExisting()
    {
        $this->skipSchemaTests();
        $this->connect($GLOBALS['DB_FULL_DSN']);   
        try{
            $this->pdo->query('DROP TABLE IF EXISTS yentu_history');
            $this->pdo->query('DROP SEQUENCE IF EXISTS yentu_history_id_seq');
        }
        catch(\PDOException $e)
        {
            
        }
        $import = new \yentu\commands\Import();
        $import->run(array());  
        $this->assertTableExists('yentu_history');
    } 
}

class NegativeMockAssertor
{
    public function __call($name, $arguments)
    {
        return false;
    }
}