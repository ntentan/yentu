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
        $description = $import->run(array());
        $newVersion = $import->getNewVersion();
        $this->assertFileExists(
            vfsStream::url("home/yentu/migrations/{$newVersion}_import.php")
        );
        require 'tests/expected/import.php';
        $descriptionArray = $description->toArray();
        unset($descriptionArray['tables']['yentu_history']);
        $this->assertEquals($expectedDescription, $descriptionArray);
    }
    
    public function testSchemaCodeGen()
    {
        $db = $this->getMockBuilder('\\yentu\\drivers\\Postgresql')->disableOriginalConstructor()->getMock();
        $db->method('getDescription')->willReturn(
            array(
                'schemata' => array(
                    'test' => array(
                        'name' => 'test',
                        'tables' =>  array(
                            'audit_trail_data' =>
                            array(
                                'schema' => '',
                                'name' => 'audit_trail_data',
                                'columns' =>
                                array(
                                    'audit_trail_data_id' =>
                                    array(
                                        'name' => 'audit_trail_data_id',
                                        'type' => 'integer',
                                        'nulls' => false,
                                    ),
                                    'audit_trail_id' =>
                                    array(
                                        'name' => 'audit_trail_id',
                                        'type' => 'integer',
                                        'nulls' => false,
                                        'default' => NULL,
                                    ),
                                    'data' =>
                                    array(
                                        'name' => 'data',
                                        'type' => 'text',
                                        'nulls' => true,
                                        'default' => NULL,
                                    ),
                                ),
                                'primary_key' =>
                                array(
                                    'audit_trail_data_id_pk' =>
                                    array(
                                        0 => 'audit_trail_data_id',
                                    ),
                                ),
                                'unique_keys' =>
                                array(
                                ),
                                'foreign_keys' =>
                                array(
                                ),
                                'indices' =>
                                array(
                                ),
                                'auto_increment' => true
                            ),
                    'api_keys' =>
                            array(
                                'schema' => '',
                                'name' => 'api_keys',
                                'columns' =>
                                array(
                                    'api_key_id' =>
                                    array(
                                        'name' => 'api_key_id',
                                        'type' => 'integer',
                                        'nulls' => false,
                                    ),
                                    'user_id' =>
                                    array(
                                        'name' => 'user_id',
                                        'type' => 'integer',
                                        'nulls' => false,
                                        'default' => NULL,
                                    ),
                                    'active' =>
                                    array(
                                        'name' => 'active',
                                        'type' => 'boolean',
                                        'nulls' => false,
                                        'default' => NULL,
                                    ),
                                    'key' =>
                                    array(
                                        'name' => 'key',
                                        'type' => 'string',
                                        'nulls' => false,
                                        'default' => NULL,
                                    ),
                                    'secret' =>
                                    array(
                                        'name' => 'secret',
                                        'type' => 'string',
                                        'nulls' => false,
                                        'default' => NULL,
                                    ),
                                ),
                                'primary_key' =>
                                array(
                                    'api_keys_pkey' =>
                                    array(
                                        0 => 'api_key_id',
                                    ),
                                ),
                                'unique_keys' =>
                                array(
                                ),
                                'foreign_keys' =>
                                array(
                                    'api_keys_user_id_fkey' =>
                                    array(
                                        'columns' =>
                                        array(
                                            0 => 'user_id',
                                        ),
                                        'foreign_columns' =>
                                        array(
                                            0 => 'user_id',
                                        ),
                                        'table' => 'api_keys',
                                        'schema' => 'test',
                                        'foreign_table' => 'users',
                                        'foreign_schema' => 'test',
                                        'on_update' => 'NO ACTION',
                                        'on_delete' => 'NO ACTION',
                                    ),
                                ),
                                'indices' =>
                                array(
                                ),
                                'auto_increment' => true,
                            )                            
                        )
                    )
                )
            )
        );
        $codeWriter = $this->getMock('\\yentu\\CodeWriter', array('getTimestamp'));
        $codeWriter->method('getTimestamp')->willReturn('25th August, 2014 14:30:13');
        
        $import = new yentu\commands\Import($db);
        $import->setCodeWriter($codeWriter);
        $import->run(array());
        $version = $import->getNewVersion();
$expected = <<< HEAD
<?php
/**
 * Generated by yentu on 25th August, 2014 14:30:13
 */
// Schemata
\$this->schema('test')
    ->table('audit_trail_data')
        ->column('audit_trail_data_id')->type('integer')->nulls(false)
        ->column('audit_trail_id')->type('integer')->nulls(false)
        ->column('data')->type('text')->nulls(true)
        ->primaryKey('audit_trail_data_id')->name('audit_trail_data_id_pk')
        ->autoIncrement()

    ->table('api_keys')
        ->column('api_key_id')->type('integer')->nulls(false)
        ->column('user_id')->type('integer')->nulls(false)
        ->column('active')->type('boolean')->nulls(false)
        ->column('key')->type('string')->nulls(false)
        ->column('secret')->type('string')->nulls(false)
        ->primaryKey('api_key_id')->name('api_keys_pkey')
        ->autoIncrement()

    ;
\$this->schema('test')->\$this->table('api_keys')
    ->foreignKey('user_id')
    ->references(\$this->refschema('test')->\$this->table('users'))
    ->columns('user_id')
    ->onDelete('NO ACTION')
    ->onUpdate('NO ACTION')
    ->name('api_keys_user_id_fkey');


HEAD;
        $this->assertStringEqualsFile(vfsStream::url("home/yentu/migrations/{$version}_import.php"), $expected);
    }
    
    /**
     * @expectedException \yentu\commands\CommandError
     */
    public function testImportNonEmptyMigrations()
    {
        file_put_contents(vfsStream::url('home/yentu/migrations/1234568901234_existing.php'), 'nothing');
        $import = new yentu\commands\Import();
        $import->run(array());        
    }
    
    public function testDatabaseNotExisting()
    {
        $this->pdo->query('DROP TABLE IF EXISTS yentu_history');
        $this->pdo->query('DROP SEQUENCE IF EXISTS yentu_history_id_seq');
        $import = new yentu\commands\Import();
        $import->run(array());  
        $this->assertTableExists('yentu_history');
    }    
    
    public function tearDown()
    {
        $this->deinitialize();
    }
}
