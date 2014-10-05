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

$this->table('users')
    ->column('branch_id')->type('integer')->nulls(true)
    ->column('picture_id')->type('integer')->nulls(true)
    ->column('department_id')->type('integer')->nulls(true)
    ->column('phone')->type('string')->nulls(true)
    ->column('email')->type('string')->nulls(false)
    ->column('user_status')->type('double')->nulls(true)
    ->column('other_names')->type('string')->nulls(true)
    ->column('last_name')->type('string')->nulls(false)
    ->column('first_name')->type('string')->nulls(false)
    ->column('role_id')->type('integer')->nulls(true)
    ->column('password')->type('string')->nulls(false)
    ->column('user_name')->type('string')->nulls(false)
    ->column('user_id')->type('integer')->nulls(false)
    ->primaryKey('user_id')->name('user_id_pk')
    ->autoIncrement()
    ->unique('user_name')->name('user_name_uk')

->table('roles')
    ->column('role_name')->type('string')->nulls(true)
    ->column('role_id')->type('integer')->nulls(false)
    ->primaryKey('role_id')->name('role_id_pk')
    ->autoIncrement()

->table('departments')
    ->column('department_name')->type('string')->nulls(false)
    ->column('department_id')->type('integer')->nulls(false)
    ->primaryKey('department_id')->name('department_id_pk')
    ->autoIncrement()

->table('temporary_roles')
    ->column('tag')->type('string')->nulls(true)
    ->column('original_role_id')->type('integer')->nulls(true)
    ->column('active')->type('boolean')->nulls(true)
    ->column('expires')->type('timestamp')->nulls(true)
    ->column('created')->type('timestamp')->nulls(true)
    ->column('user_id')->type('integer')->nulls(true)
    ->column('new_role_id')->type('integer')->nulls(true)
    ->column('temporary_role_id')->type('integer')->nulls(false)
    ->primaryKey('temporary_role_id')->name('temporary_role_id_pk')
    ->autoIncrement()

->table('suppliers')
    ->column('user_id')->type('integer')->nulls(true)
    ->column('telephone')->type('string')->nulls(true)
    ->column('address')->type('string')->nulls(true)
    ->column('supplier_name')->type('string')->nulls(false)
    ->column('supplier_id')->type('integer')->nulls(false)
    ->primaryKey('supplier_id')->name('supplier_id_pk')
    ->autoIncrement()

->table('countries')
    ->column('currency_symbol')->type('string')->nulls(true)
    ->column('currency')->type('string')->nulls(true)
    ->column('nationality')->type('string')->nulls(true)
    ->column('country_code')->type('string')->nulls(true)
    ->column('country_name')->type('string')->nulls(false)
    ->column('country_id')->type('integer')->nulls(false)
    ->primaryKey('country_id')->name('country_id_pk')
    ->autoIncrement()
    ->unique('country_name')->name('country_name_uk')

->table('permissions')
    ->column('module')->type('string')->nulls(true)
    ->column('value')->type('double')->nulls(false)
    ->column('permission')->type('string')->nulls(true)
    ->column('role_id')->type('integer')->nulls(false)
    ->column('permission_id')->type('integer')->nulls(false)
    ->primaryKey('permission_id')->name('perm_id_pk')
    ->autoIncrement()

->table('notes')
    ->column('user_id')->type('integer')->nulls(false)
    ->column('item_type')->type('string')->nulls(false)
    ->column('item_id')->type('integer')->nulls(false)
    ->column('note_time')->type('timestamp')->nulls(false)
    ->column('note')->type('string')->nulls(true)
    ->column('note_id')->type('integer')->nulls(false)
    ->primaryKey('note_id')->name('notes_note_id_pk')
    ->autoIncrement()

->table('regions')
    ->column('country_id')->type('integer')->nulls(false)
    ->column('name')->type('string')->nulls(false)
    ->column('region_code')->type('string')->nulls(false)
    ->column('region_id')->type('integer')->nulls(false)
    ->primaryKey('region_id')->name('region_id_pk')
    ->autoIncrement()
    ->unique('name')->name('region_name_uk')

->table('note_attachments')
    ->column('object_id')->type('integer')->nulls(true)
    ->column('note_id')->type('integer')->nulls(true)
    ->column('description')->type('string')->nulls(true)
    ->column('note_attachment_id')->type('integer')->nulls(false)
    ->primaryKey('note_attachment_id')->name('note_attachments_pkey')
    ->autoIncrement()

->table('branches')
    ->column('city_id')->type('integer')->nulls(true)
    ->column('address')->type('text')->nulls(true)
    ->column('branch_name')->type('string')->nulls(false)
    ->column('branch_id')->type('integer')->nulls(false)
    ->primaryKey('branch_id')->name('branches_pkey')
    ->autoIncrement()

->table('clients')
    ->column('branch_id')->type('integer')->nulls(false)
    ->column('marital_status')->type('double')->nulls(true)
    ->column('gender')->type('double')->nulls(true)
    ->column('nationality_id')->type('integer')->nulls(true)
    ->column('company_name')->type('string')->nulls(true)
    ->column('contact_person')->type('string')->nulls(true)
    ->column('in_trust_for')->type('string')->nulls(true)
    ->column('country_id')->type('integer')->nulls(true)
    ->column('id_issue_date')->type('date')->nulls(true)
    ->column('id_issue_place')->type('string')->nulls(true)
    ->column('signature')->type('string')->nulls(true)
    ->column('scanned_id')->type('string')->nulls(true)
    ->column('picture')->type('string')->nulls(true)
    ->column('occupation')->type('string')->nulls(true)
    ->column('residential_status')->type('double')->nulls(true)
    ->column('id_number')->type('string')->nulls(false)
    ->column('id_type_id')->type('integer')->nulls(false)
    ->column('city_id')->type('integer')->nulls(true)
    ->column('birth_date')->type('date')->nulls(true)
    ->column('email')->type('string')->nulls(true)
    ->column('fax')->type('string')->nulls(true)
    ->column('mobile')->type('string')->nulls(true)
    ->column('contact_tel')->type('string')->nulls(true)
    ->column('residential_address')->type('string')->nulls(true)
    ->column('mailing_address')->type('string')->nulls(false)
    ->column('previous_names')->type('string')->nulls(true)
    ->column('other_names')->type('string')->nulls(true)
    ->column('first_name')->type('string')->nulls(true)
    ->column('surname')->type('string')->nulls(true)
    ->column('title')->type('string')->nulls(true)
    ->column('account_type')->type('double')->nulls(false)
    ->column('main_client_id')->type('integer')->nulls(false)
    ->primaryKey('main_client_id')->name('clients_main_client_id_pk')
    ->autoIncrement()

->table('identification_types')
    ->column('id_name')->type('string')->nulls(false)
    ->column('id_type_id')->type('integer')->nulls(false)
    ->primaryKey('id_type_id')->name('id_type_id_pk')
    ->autoIncrement()

->table('client_users')
    ->column('status')->type('integer')->nulls(false)
    ->column('user_name')->type('string')->nulls(false)
    ->column('password')->type('string')->nulls(false)
    ->column('membership_id')->type('integer')->nulls(true)
    ->column('main_client_id')->type('integer')->nulls(true)
    ->column('client_user_id')->type('integer')->nulls(false)
    ->primaryKey('client_user_id')->name('client_users_client_user_id_pk')
    ->autoIncrement()

->table('client_joint_accounts')
    ->column('mobile')->type('string')->nulls(true)
    ->column('id_issue_date')->type('date')->nulls(true)
    ->column('id_issue_place')->type('string')->nulls(true)
    ->column('id_number')->type('string')->nulls(true)
    ->column('id_type_id')->type('integer')->nulls(true)
    ->column('office_telephone')->type('string')->nulls(true)
    ->column('telephone')->type('string')->nulls(true)
    ->column('address')->type('string')->nulls(true)
    ->column('previous_names')->type('string')->nulls(true)
    ->column('other_names')->type('string')->nulls(true)
    ->column('first_name')->type('string')->nulls(false)
    ->column('surname')->type('string')->nulls(false)
    ->column('title')->type('string')->nulls(true)
    ->column('main_client_id')->type('integer')->nulls(false)
    ->column('joint_account_id')->type('integer')->nulls(false)
    ->primaryKey('joint_account_id')->name('joint_account_id_pk')
    ->autoIncrement()

->table('cities')
    ->column('region_id')->type('integer')->nulls(false)
    ->column('city_name')->type('string')->nulls(false)
    ->column('city_id')->type('integer')->nulls(false)
    ->primaryKey('city_id')->name('city_id_pk')
    ->autoIncrement()
    ->unique('city_name')->name('city_name_uk')

->table('bank_branches')
    ->column('sort_code')->type('string')->nulls(false)->defaultValue("0")
    ->column('address')->type('string')->nulls(true)
    ->column('branch_name')->type('string')->nulls(false)
    ->column('bank_id')->type('integer')->nulls(false)
    ->column('bank_branch_id')->type('integer')->nulls(false)
    ->primaryKey('bank_branch_id')->name('bank_branch_id_pk')
    ->autoIncrement()

->table('banks')
    ->column('swift_code')->type('string')->nulls(true)->defaultValue("0")
    ->column('bank_code')->type('string')->nulls(false)->defaultValue("0")
    ->column('bank_name')->type('string')->nulls(false)
    ->column('bank_id')->type('integer')->nulls(false)
    ->primaryKey('bank_id')->name('bank_id_pk')
    ->autoIncrement()
    ->unique('bank_name')->name('bank_name_uk')

->table('api_keys')
    ->column('secret')->type('string')->nulls(false)
    ->column('key')->type('string')->nulls(false)
    ->column('active')->type('boolean')->nulls(false)
    ->column('user_id')->type('integer')->nulls(false)
    ->column('api_key_id')->type('integer')->nulls(false)
    ->primaryKey('api_key_id')->name('api_keys_pkey')
    ->autoIncrement()

->table('relationships')
    ->column('relationship_name')->type('string')->nulls(false)
    ->column('relationship_id')->type('integer')->nulls(false)
    ->primaryKey('relationship_id')->name('relationship_id_pk')
    ->autoIncrement()

->table('notifications')
    ->column('subject')->type('string')->nulls(true)
    ->column('email')->type('text')->nulls(true)
    ->column('sms')->type('text')->nulls(true)
    ->column('tag')->type('string')->nulls(true)
    ->column('notification_id')->type('integer')->nulls(false)
    ->primaryKey('notification_id')->name('notifications_pkey')
    ->autoIncrement()

->table('holidays')
    ->column('holiday_date')->type('date')->nulls(false)
    ->column('name')->type('string')->nulls(false)
    ->column('holiday_id')->type('integer')->nulls(false)
    ->primaryKey('holiday_id')->name('holidays_holiday_id_pk')
    ->autoIncrement()

->table('configurations')
    ->column('value')->type('string')->nulls(true)
    ->column('key')->type('string')->nulls(true)
    ->column('configuration_id')->type('integer')->nulls(false)
    ->primaryKey('configuration_id')->name('configuration_id_pk')
    ->autoIncrement()
    ->unique('key')->name('key_uk')

->table('cheque_formats')
    ->column('data')->type('text')->nulls(false)
    ->column('description')->type('string')->nulls(false)
    ->column('cheque_format_id')->type('integer')->nulls(false)
    ->primaryKey('cheque_format_id')->name('cheque_formats_cheque_format_id_pk')
    ->autoIncrement()

->table('binary_objects')
    ->column('data')->type('blob')->nulls(true)
    ->column('object_id')->type('integer')->nulls(false)
    ->primaryKey('object_id')->name('binary_objects_pkey')
    ->autoIncrement()

->table('audit_trail_data')
    ->column('data')->type('text')->nulls(true)
    ->column('audit_trail_id')->type('integer')->nulls(false)
    ->column('audit_trail_data_id')->type('integer')->nulls(false)
    ->primaryKey('audit_trail_data_id')->name('audit_trail_data_id_pk')
    ->autoIncrement()
    ->index('audit_trail_id')->name('audit_trail_id_idx')

->table('audit_trail')
    ->column('data')->type('text')->nulls(true)
    ->column('type')->type('double')->nulls(false)
    ->column('audit_date')->type('timestamp')->nulls(false)
    ->column('description')->type('string')->nulls(false)
    ->column('item_type')->type('string')->nulls(false)
    ->column('item_id')->type('integer')->nulls(false)
    ->column('user_id')->type('integer')->nulls(false)
    ->column('audit_trail_id')->type('integer')->nulls(false)
    ->primaryKey('audit_trail_id')->name('audit_trail_audit_id_pk')
    ->autoIncrement()
    ->index('item_id')->name('audit_trail_item_id_idx')

->table('locations')
    ->column('address')->type('string')->nulls(true)
    ->column('name')->type('string')->nulls(true)
    ->column('location_id')->type('integer')->nulls(false)
    ->primaryKey('location_id')->name('location_id_pk')
    ->autoIncrement()
        
->view('users_view')->definition("SELECT * FROM users JOIN roles USING (role_id)");
$this->view('countries_view')->definition("SELECT * FROM countries JOIN regions USING (country_id)");

$this->table('users')
    ->foreignKey('branch_id')
    ->references($this->reftable('branches'))
    ->columns('branch_id')
    ->name('users_branch_id_fk');

$this->table('users')
    ->foreignKey('department_id')
    ->references($this->reftable('departments'))
    ->columns('department_id')
    ->name('users_dept_id_fk');

$this->table('users')
    ->foreignKey('role_id')
    ->references($this->reftable('roles'))
    ->columns('role_id')
    ->name('users_role_id_fk');

$this->table('temporary_roles')
    ->foreignKey('new_role_id')
    ->references($this->reftable('roles'))
    ->columns('role_id')
    ->name('temporary_rol_new_role_id_fk');

$this->table('temporary_roles')
    ->foreignKey('original_role_id')
    ->references($this->reftable('roles'))
    ->columns('role_id')
    ->name('temporary_rol_orig_role_id_fk');

$this->table('temporary_roles')
    ->foreignKey('user_id')
    ->references($this->reftable('users'))
    ->columns('user_id')
    ->name('temporary_roles_user_id_fk');

$this->table('suppliers')
    ->foreignKey('user_id')
    ->references($this->reftable('users'))
    ->columns('user_id')
    ->name('suppliers_user_id_fk');

$this->table('permissions')
    ->foreignKey('role_id')
    ->references($this->reftable('roles'))
    ->columns('role_id')
    ->name('permissios_role_id_fk');

$this->table('notes')
    ->foreignKey('user_id')
    ->references($this->reftable('users'))
    ->columns('user_id')
    ->name('notes_user_id_fk');

$this->table('regions')
    ->foreignKey('country_id')
    ->references($this->reftable('countries'))
    ->columns('country_id')
    ->name('regions_country_id_fk');

$this->table('note_attachments')
    ->foreignKey('note_id')
    ->references($this->reftable('notes'))
    ->columns('note_id')
    ->name('note_attachments_note_id_fkey');

$this->table('clients')
    ->foreignKey('branch_id')
    ->references($this->reftable('branches'))
    ->columns('branch_id')
    ->name('clients_branch_id_fkey');

$this->table('clients')
    ->foreignKey('city_id')
    ->references($this->reftable('cities'))
    ->columns('city_id')
    ->name('clients_city_id_fk');

$this->table('clients')
    ->foreignKey('country_id')
    ->references($this->reftable('countries'))
    ->columns('country_id')
    ->name('clients_country_id_fk');

$this->table('clients')
    ->foreignKey('id_type_id')
    ->references($this->reftable('identification_types'))
    ->columns('id_type_id')
    ->name('clients_id_type_id_fk');

$this->table('clients')
    ->foreignKey('nationality_id')
    ->references($this->reftable('countries'))
    ->columns('country_id')
    ->name('clients_nationality_id_fk');

$this->table('client_users')
    ->foreignKey('main_client_id')
    ->references($this->reftable('clients'))
    ->columns('main_client_id')
    ->name('client_users_main_client_id_fk');

$this->table('client_joint_accounts')
    ->foreignKey('id_type_id')
    ->references($this->reftable('identification_types'))
    ->columns('id_type_id')
    ->name('client_joint_id_type_id_fk');

$this->table('client_joint_accounts')
    ->foreignKey('main_client_id')
    ->references($this->reftable('clients'))
    ->columns('main_client_id')
    ->name('client_joint_main_client_id_fk');

$this->table('cities')
    ->foreignKey('region_id')
    ->references($this->reftable('regions'))
    ->columns('region_id')
    ->name('cities_region_id_fk');

$this->table('bank_branches')
    ->foreignKey('bank_id')
    ->references($this->reftable('banks'))
    ->columns('bank_id')
    ->name('branch_bank_id_fk');

$this->table('api_keys')
    ->foreignKey('user_id')
    ->references($this->reftable('users'))
    ->columns('user_id')
    ->name('api_keys_user_id_fkey');



