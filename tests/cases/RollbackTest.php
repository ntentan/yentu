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
use ntentan\config\Config;

class RollbackTest extends \yentu\tests\YentuTest
{
    public function setUp()
    {
        $this->testDatabase = 'yentu_rollback_test';
        parent::setup();
        $this->createDb($GLOBALS['DB_NAME']);
        $this->initDb($GLOBALS['DB_FULL_DSN'], file_get_contents("tests/sql/{$GLOBALS['DRIVER']}/pre_rollback.sql"));
        $this->connect($GLOBALS['DB_FULL_DSN']);
        $this->setupStreams();
        $init = new \yentu\commands\Init();
        $init->createConfigFile(
            array(
                'driver' => $GLOBALS['DRIVER'],
                'host' => $GLOBALS['DB_HOST'],
                'dbname' => $GLOBALS["DB_NAME"],
                'user' => $GLOBALS['DB_USER'],
                'password' => $GLOBALS['DB_PASSWORD'],
                'file' => $GLOBALS['DB_FILE']
            )
        );
        Config::readPath(\yentu\Yentu::getPath('config'), 'yentu');
    }
    
    public function testRollback()
    {
        foreach($this->tables as $table)
        {
            $this->assertTableExists($table);     
        }
        
        $rollback = new \yentu\commands\Rollback();
        $rollback->run(array());
        
        foreach($this->tables as $table)
        {
            if($table == 'yentu_history') continue;
            $this->assertTableDoesntExist($table);            
        }
    }
    
    private $tables = array(
        'api_keys',
        'audit_trail',
        'audit_trail_data',
        'bank_branches',
        'banks',
        'binary_objects',
        'branches',
        'cheque_formats',
        'cities',
        'client_joint_accounts',
        'clients',
        'client_users',
        'configurations',
        'countries',
        'departments',
        'holidays',
        'identification_types',
        'locations',
        'note_attachments',
        'notes',
        'notifications',
        'permissions',
        'regions',
        'relationships',
        'roles',
        'suppliers',
        'temporary_roles',
        'users',
        'yentu_history'
    );
}

