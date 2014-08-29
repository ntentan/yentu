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

class MigrateTest extends \yentu\tests\YentuTest
{
    public function setup()
    {
        $this->initialize('MIGRATE');
    }
    
    public function testMigration()
    {
        copy('tests/migrations/12345678901234_import.php', vfsStream::url('home/yentu/migrations/12345678901234_import.php'));
        $migrate = new yentu\commands\Migrate();
        $migrate->run(array());
        $this->assertEquals(
            "Yentu successfully initialized.\nApplying 'import' migration\n", 
            file_get_contents(vfsStream::url('home/output.txt'))
        );
    }
    
    /**
     * @dataProvider tablesProvider
     * @depends testMigration
     */
    public function testTables($table)
    {
        $this->assertTableExists($table);
    }

    public function testChangeNulls()
    {
        copy('tests/migrations/12345678901234_change_null.php', vfsStream::url('home/yentu/migrations/12345678901234_change_null.php'));
        $migrate = new yentu\commands\Migrate();
        $migrate->run(array());
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
    }
}