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

class MigrateTest extends \yentu\tests\YentuTest
{

    public function setup()
    {
        $this->testDatabase = 'yentu_migration_test';
        parent::setup();        
        $this->setupForMigration();
    }
    
    public function testMigration()
    {
        copy('tests/migrations/12345678901234_import.php', vfsStream::url('home/yentu/migrations/12345678901234_import.php'));
        $migrate = new \yentu\commands\Migrate();
        $migrate->run(array());
        
        $this->assertEquals(
            file_get_contents("tests/streams/migrate_output.txt"), 
            file_get_contents(vfsStream::url('home/output.txt'))
        );
        
        foreach($this->tables as $table)
        {
            $this->assertTableExists($table);        
        }
        copy('tests/migrations/12345678901234_change_null.php', vfsStream::url('home/yentu/migrations/12345678901235_change_null.php'));
        $migrate = new \yentu\commands\Migrate();
        $this->assertColumnNullable('role_name', 'roles');
        $this->assertColumnExists('user_name', 'users');
        $migrate->run(array());
        $this->assertColumnNotNullable('role_name', 'roles');        
        $this->assertColumnExists('username', 'users');
    }
    
    
    public function testSchemaMigration()
    {
        $this->skipSchemaTests();
        copy('tests/migrations/12345678901234_schema.php', vfsStream::url('home/yentu/migrations/12345678901234_schema.php'));        
        $migrate = new \yentu\commands\Migrate();
        $migrate->run(array());
        $this->assertSchemaExists('schema');

        foreach($this->tables as $table)
        {
            if($table == 'yentu_history') return;        
            $schema = array_search($table, array('cities', 'locations', 'countries', 'regions', 'countries_view')) === false ? 'schema' : 'geo';
            $this->assertTableExists(array('table'=>$table, 'schema' => $schema));
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
        'yentu_history',
        'users_view',
        'countries_view'
    );
}

