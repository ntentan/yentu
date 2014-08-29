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

class RollbackTest extends \yentu\tests\YentuTest
{
    public function setup()
    {
        $this->pdo = new \PDO($GLOBALS["ROLLBACK_DB_DSN"], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWORD']); 
        
        vfsStream::setup('home');        
        \yentu\Yentu::setDefaultHome(vfsStream::url('home/yentu'));
        \yentu\Yentu::setOutputStreamUrl(vfsStream::url('home/output.txt'));        
        
        $init = new \yentu\commands\Init();
        $init->createConfigFile(
            array(
                'driver' => 'postgresql',
                'host' => $GLOBALS['DB_HOST'],
                'dbname' => $GLOBALS["ROLLBACK_DB_NAME"],
                'user' => $GLOBALS['DB_USER'],
                'password' => $GLOBALS['DB_PASSWORD']
            )
        );
    }
    
    public function testRollback()
    {
        $rollback = new yentu\commands\Rollback();
        $rollback->run(array());
    }
    
    /**
     * @dataProvider tablesProvider
     * @depends testMigration
     */
    /*public function testTables($table)
    {
        $this->assertTableExists($table);
        $this->clearHistory = false;
    }

    public function testChangeNulls()
    {
        copy('tests/migrations/12345678901234_change_null.php', vfsStream::url('home/yentu/migrations/12345678901234_change_null.php'));
        $migrate = new yentu\commands\Migrate();
        $this->assertColumnNullable('role_name', 'roles');
        $migrate->run(array());
        $this->assertColumnNotNullable('role_name', 'roles');
        $this->clearHistory = false;
    }
    
    public function tablesProvider()
    {
        return array(
            array('api_keys'),
            array('audit_trail'),
            array('audit_trail_data'),
            array('bank_branches'),
            array('banks'),
            array('binary_objects'),
            array('branches'),
            array('cheque_formats'),
            array('cities'),
            array('client_joint_accounts'),
            array('clients'),
            array('client_users'),
            array('configurations'),
            array('countries'),
            array('departments'),
            array('holidays'),
            array('identification_types'),
            array('locations'),
            array('note_attachments'),
            array('notes'),
            array('notifications'),
            array('permissions'),
            array('regions'),
            array('relationships'),
            array('roles'),
            array('suppliers'),
            array('temporary_roles'),
            array('users'),
            array('yentu_history'),
        );
    }*/
}

