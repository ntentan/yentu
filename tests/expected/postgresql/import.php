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

$expectedDescription = array (
  'schemata' => 
  array (
  ),
  'tables' => 
  array (
    'api_keys' => 
    array (
      'schema' => '',
      'name' => 'api_keys',
      'columns' => 
      array (
        'active' => 
        array (
          'name' => 'active',
          'type' => 'boolean',
          'nulls' => false,
          'default' => NULL,
          'length' => NULL,
        ),
        'api_key_id' => 
        array (
          'name' => 'api_key_id',
          'type' => 'integer',
          'nulls' => false,
          'default' => NULL,
          'length' => NULL,
        ),
        'key' => 
        array (
          'name' => 'key',
          'type' => 'string',
          'nulls' => false,
          'default' => NULL,
          'length' => 512,
        ),
        'secret' => 
        array (
          'name' => 'secret',
          'type' => 'string',
          'nulls' => false,
          'default' => NULL,
          'length' => 512,
        ),
        'user_id' => 
        array (
          'name' => 'user_id',
          'type' => 'integer',
          'nulls' => false,
          'default' => NULL,
          'length' => NULL,
        ),
      ),
      'primary_key' => 
      array (
        'api_keys_pkey' => 
        array (
          'columns' => 
          array (
            0 => 'api_key_id',
          ),
        ),
      ),
      'unique_keys' => 
      array (
      ),
      'foreign_keys' => 
      array (
        'api_keys_user_id_fkey' => 
        array (
          'schema' => '',
          'table' => 'api_keys',
          'columns' => 
          array (
            0 => 'user_id',
          ),
          'foreign_table' => 'users',
          'foreign_schema' => '',
          'foreign_columns' => 
          array (
            0 => 'user_id',
          ),
          'on_update' => 'NO ACTION',
          'on_delete' => 'NO ACTION',
        ),
      ),
      'indices' => 
      array (
      ),
      'auto_increment' => true,
    ),
    'audit_trail' => 
    array (
      'schema' => '',
      'name' => 'audit_trail',
      'columns' => 
      array (
        'audit_date' => 
        array (
          'name' => 'audit_date',
          'type' => 'timestamp',
          'nulls' => false,
          'default' => NULL,
          'length' => NULL,
        ),
        'audit_trail_id' => 
        array (
          'name' => 'audit_trail_id',
          'type' => 'integer',
          'nulls' => false,
          'default' => NULL,
          'length' => NULL,
        ),
        'data' => 
        array (
          'name' => 'data',
          'type' => 'text',
          'nulls' => true,
          'default' => NULL,
          'length' => NULL,
        ),
        'description' => 
        array (
          'name' => 'description',
          'type' => 'string',
          'nulls' => false,
          'default' => NULL,
          'length' => 4000,
        ),
        'item_id' => 
        array (
          'name' => 'item_id',
          'type' => 'integer',
          'nulls' => false,
          'default' => NULL,
          'length' => NULL,
        ),
        'item_type' => 
        array (
          'name' => 'item_type',
          'type' => 'string',
          'nulls' => false,
          'default' => NULL,
          'length' => 64,
        ),
        'type' => 
        array (
          'name' => 'type',
          'type' => 'double',
          'nulls' => false,
          'default' => NULL,
          'length' => NULL,
        ),
        'user_id' => 
        array (
          'name' => 'user_id',
          'type' => 'integer',
          'nulls' => false,
          'default' => NULL,
          'length' => NULL,
        ),
      ),
      'primary_key' => 
      array (
        'audit_trail_audit_id_pk' => 
        array (
          'columns' => 
          array (
            0 => 'audit_trail_id',
          ),
        ),
      ),
      'unique_keys' => 
      array (
      ),
      'foreign_keys' => 
      array (
        'audit_trail_user_id_fk' => 
        array (
          'schema' => '',
          'table' => 'audit_trail',
          'columns' => 
          array (
            0 => 'user_id',
          ),
          'foreign_table' => 'users',
          'foreign_schema' => '',
          'foreign_columns' => 
          array (
            0 => 'user_id',
          ),
          'on_update' => 'NO ACTION',
          'on_delete' => 'SET NULL',
        ),
      ),
      'indices' => 
      array (
        'audit_trail_item_id_idx' => 
        array (
          'table_name' => 'audit_trail',
          'columns' => 
          array (
            0 => 'item_id',
          ),
        ),
        'audit_trail_item_type_idx' => 
        array (
          'table_name' => 'audit_trail',
          'columns' => 
          array (
            0 => 'item_type',
          ),
        ),
      ),
      'auto_increment' => true,
    ),
    'audit_trail_data' => 
    array (
      'schema' => '',
      'name' => 'audit_trail_data',
      'columns' => 
      array (
        'audit_trail_data_id' => 
        array (
          'name' => 'audit_trail_data_id',
          'type' => 'integer',
          'nulls' => false,
          'default' => NULL,
          'length' => NULL,
        ),
        'audit_trail_id' => 
        array (
          'name' => 'audit_trail_id',
          'type' => 'integer',
          'nulls' => false,
          'default' => NULL,
          'length' => NULL,
        ),
        'data' => 
        array (
          'name' => 'data',
          'type' => 'text',
          'nulls' => true,
          'default' => NULL,
          'length' => NULL,
        ),
      ),
      'primary_key' => 
      array (
        'audit_trail_data_id_pk' => 
        array (
          'columns' => 
          array (
            0 => 'audit_trail_data_id',
          ),
        ),
      ),
      'unique_keys' => 
      array (
      ),
      'foreign_keys' => 
      array (
      ),
      'indices' => 
      array (
      ),
      'auto_increment' => true,
    ),
    'keystore' => 
    array (
      'schema' => '',
      'name' => 'keystore',
      'columns' => 
      array (
        'key' => 
        array (
          'name' => 'key',
          'type' => 'string',
          'nulls' => false,
          'default' => NULL,
          'length' => 255,
        ),
        'keystore_id' => 
        array (
          'name' => 'keystore_id',
          'type' => 'integer',
          'nulls' => false,
          'default' => NULL,
          'length' => NULL,
        ),
        'value' => 
        array (
          'name' => 'value',
          'type' => 'text',
          'nulls' => true,
          'default' => NULL,
          'length' => NULL,
        ),
      ),
      'primary_key' => 
      array (
        'keystore_pkey' => 
        array (
          'columns' => 
          array (
            0 => 'keystore_id',
          ),
        ),
      ),
      'unique_keys' => 
      array (
        'keystore_key_key' => 
        array (
          'columns' => 
          array (
            0 => 'key',
          ),
        ),
      ),
      'foreign_keys' => 
      array (
      ),
      'indices' => 
      array (
      ),
      'auto_increment' => true,
    ),
    'permissions' => 
    array (
      'schema' => '',
      'name' => 'permissions',
      'columns' => 
      array (
        'module' => 
        array (
          'name' => 'module',
          'type' => 'string',
          'nulls' => true,
          'default' => NULL,
          'length' => 4000,
        ),
        'permission' => 
        array (
          'name' => 'permission',
          'type' => 'string',
          'nulls' => true,
          'default' => NULL,
          'length' => 4000,
        ),
        'permission_id' => 
        array (
          'name' => 'permission_id',
          'type' => 'integer',
          'nulls' => false,
          'default' => NULL,
          'length' => NULL,
        ),
        'role_id' => 
        array (
          'name' => 'role_id',
          'type' => 'integer',
          'nulls' => false,
          'default' => NULL,
          'length' => NULL,
        ),
        'value' => 
        array (
          'name' => 'value',
          'type' => 'double',
          'nulls' => false,
          'default' => NULL,
          'length' => NULL,
        ),
      ),
      'primary_key' => 
      array (
        'perm_id_pk' => 
        array (
          'columns' => 
          array (
            0 => 'permission_id',
          ),
        ),
      ),
      'unique_keys' => 
      array (
      ),
      'foreign_keys' => 
      array (
        'permissios_role_id_fk' => 
        array (
          'schema' => '',
          'table' => 'permissions',
          'columns' => 
          array (
            0 => 'role_id',
          ),
          'foreign_table' => 'roles',
          'foreign_schema' => '',
          'foreign_columns' => 
          array (
            0 => 'role_id',
          ),
          'on_update' => 'NO ACTION',
          'on_delete' => 'CASCADE',
        ),
      ),
      'indices' => 
      array (
      ),
      'auto_increment' => true,
    ),
    'roles' => 
    array (
      'schema' => '',
      'name' => 'roles',
      'columns' => 
      array (
        'role_id' => 
        array (
          'name' => 'role_id',
          'type' => 'integer',
          'nulls' => false,
          'default' => NULL,
          'length' => NULL,
        ),
        'role_name' => 
        array (
          'name' => 'role_name',
          'type' => 'string',
          'nulls' => true,
          'default' => NULL,
          'length' => 64,
        ),
      ),
      'primary_key' => 
      array (
        'role_id_pk' => 
        array (
          'columns' => 
          array (
            0 => 'role_id',
          ),
        ),
      ),
      'unique_keys' => 
      array (
      ),
      'foreign_keys' => 
      array (
      ),
      'indices' => 
      array (
      ),
      'auto_increment' => true,
    ),
    'users' => 
    array (
      'schema' => '',
      'name' => 'users',
      'columns' => 
      array (
        'email' => 
        array (
          'name' => 'email',
          'type' => 'string',
          'nulls' => false,
          'default' => NULL,
          'length' => 64,
        ),
        'first_name' => 
        array (
          'name' => 'first_name',
          'type' => 'string',
          'nulls' => false,
          'default' => NULL,
          'length' => 64,
        ),
        'last_name' => 
        array (
          'name' => 'last_name',
          'type' => 'string',
          'nulls' => false,
          'default' => NULL,
          'length' => 64,
        ),
        'other_names' => 
        array (
          'name' => 'other_names',
          'type' => 'string',
          'nulls' => true,
          'default' => NULL,
          'length' => 64,
        ),
        'password' => 
        array (
          'name' => 'password',
          'type' => 'string',
          'nulls' => false,
          'default' => NULL,
          'length' => 64,
        ),
        'role_id' => 
        array (
          'name' => 'role_id',
          'type' => 'integer',
          'nulls' => true,
          'default' => NULL,
          'length' => NULL,
        ),
        'user_id' => 
        array (
          'name' => 'user_id',
          'type' => 'integer',
          'nulls' => false,
          'default' => NULL,
          'length' => NULL,
        ),
        'user_name' => 
        array (
          'name' => 'user_name',
          'type' => 'string',
          'nulls' => false,
          'default' => NULL,
          'length' => 64,
        ),
        'user_status' => 
        array (
          'name' => 'user_status',
          'type' => 'double',
          'nulls' => true,
          'default' => '2',
          'length' => NULL,
        ),
      ),
      'primary_key' => 
      array (
        'user_id_pk' => 
        array (
          'columns' => 
          array (
            0 => 'user_id',
          ),
        ),
      ),
      'unique_keys' => 
      array (
        'user_name_uk' => 
        array (
          'columns' => 
          array (
            0 => 'user_name',
          ),
        ),
      ),
      'foreign_keys' => 
      array (
        'users_role_id_fk' => 
        array (
          'schema' => '',
          'table' => 'users',
          'columns' => 
          array (
            0 => 'role_id',
          ),
          'foreign_table' => 'roles',
          'foreign_schema' => '',
          'foreign_columns' => 
          array (
            0 => 'role_id',
          ),
          'on_update' => 'NO ACTION',
          'on_delete' => 'SET NULL',
        ),
      ),
      'indices' => 
      array (
      ),
      'auto_increment' => true,
    ),
  ),
  'views' => 
  array (
  ),
);
