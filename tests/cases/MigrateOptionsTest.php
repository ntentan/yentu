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

class MigrateOptionsTest extends \yentu\tests\YentuTest
{
    public function setUp()
    {
        $this->testDatabase = 'yentu_migration_test';
        parent::setup();
        $this->setupForMigration();
    }
    
    public function testMigration()
    {
        copy('tests/migrations/12345678901234_import.php', vfsStream::url('home/yentu/migrations/12345678901234_import.php'));
        
        $migrate = new \yentu\commands\Migrate();
        $migrate->run(array('no-foreign-keys' => true));
        $this->assertEquals(
            file_get_contents("tests/streams/migrate_options_output_1.txt"), 
            file_get_contents(vfsStream::url('home/output.txt'))
        );
        
        foreach($this->fkeys as $fkey)
        {
            $this->assertForeignKeyDoesntExist($fkey);
        }
 
        file_put_contents(vfsStream::url("home/output.txt"), '');
        
        $migrate->run(array('only-foreign-keys' => true));
        $this->assertEquals(
            file_get_contents("tests/streams/migrate_options_output_2.txt"), 
            file_get_contents(vfsStream::url('home/output.txt'))
        );  
        
        foreach($this->fkeys as $fkey)
        {
            $this->assertForeignKeyExists($fkey);
        }        
    }
        
    private $fkeys = array(
        array("table" => "users", "name" => "users_branch_id_branches_branch_id_fk"),
        array("table" => "users", "name" => "users_department_id_departments_department_id_fk"),
        array("table" => "users", "name" => "users_role_id_fk"),
        array("table" => "temporary_roles", "name" => "temporary_rol_new_role_id_fk"),
        array("table" => "temporary_roles", "name" => "temporary_rol_orig_role_id_fk"),
        array("table" => "temporary_roles", "name" => "temporary_roles_user_id_fk"),
        array("table" => "suppliers", "name" => "suppliers_user_id_fk"),
        array("table" => "permissions", "name" => "permissios_role_id_fk"),
        array("table" => "notes", "name" => "notes_user_id_fk"),
        array("table" => "regions", "name" => "regions_country_id_fk"),
        array("table" => "note_attachments", "name" => "note_attachments_note_id_fkey"),
        array("table" => "clients", "name" => "clients_branch_id_fkey"),
        array("table" => "clients", "name" => "clients_city_id_fk"),
        array("table" => "clients", "name" => "clients_country_id_fk"),
        array("table" => "clients", "name" => "clients_id_type_id_fk"),
        array("table" => "clients", "name" => "clients_nationality_id_fk"),
        array("table" => "client_users", "name" => "client_users_main_client_id_fk"),
        array("table" => "client_joint_accounts", "name" => "client_joint_id_type_id_fk"),
        array("table" => "client_joint_accounts", "name" => "client_joint_main_client_id_fk"),
        array("table" => "cities", "name" => "cities_region_id_fk"),
        array("table" => "bank_branches", "name" => "branch_bank_id_fk"),
        array("table" => "api_keys", "name" => "api_keys_user_id_fkey")
    );
}



