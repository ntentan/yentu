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

$expectedDescription = array(
    'schemata' => 
    array (
    ),
    'tables' => 
    array (
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
          ),
          'audit_trail_id' => 
          array (
            'name' => 'audit_trail_id',
            'type' => 'integer',
            'nulls' => false,
            'default' => NULL,
          ),
          'data' => 
          array (
            'name' => 'data',
            'type' => 'text',
            'nulls' => true,
            'default' => NULL,
          ),
        ),
        'primary_key' => 
        array (
          'audit_trail_data_id_pk' => 
          array (
            0 => 'audit_trail_data_id',
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
          'keystore_id' => 
          array (
            'name' => 'keystore_id',
            'type' => 'integer',
            'nulls' => false,
          ),
          'key' => 
          array (
            'name' => 'key',
            'type' => 'string',
            'nulls' => false,
            'default' => NULL,
          ),
          'value' => 
          array (
            'name' => 'value',
            'type' => 'text',
            'nulls' => true,
            'default' => NULL,
          ),
        ),
        'primary_key' => 
        array (
          'keystore_pkey' => 
          array (
            0 => 'keystore_id',
          ),
        ),
        'unique_keys' => 
        array (
          'keystore_key_key' => 
          array (
            0 => 'key',
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
      'api_keys' => 
      array (
        'schema' => '',
        'name' => 'api_keys',
        'columns' => 
        array (
          'api_key_id' => 
          array (
            'name' => 'api_key_id',
            'type' => 'integer',
            'nulls' => false,
          ),
          'user_id' => 
          array (
            'name' => 'user_id',
            'type' => 'integer',
            'nulls' => false,
            'default' => NULL,
          ),
          'active' => 
          array (
            'name' => 'active',
            'type' => 'boolean',
            'nulls' => false,
            'default' => NULL,
          ),
          'key' => 
          array (
            'name' => 'key',
            'type' => 'string',
            'nulls' => false,
            'default' => NULL,
          ),
          'secret' => 
          array (
            'name' => 'secret',
            'type' => 'string',
            'nulls' => false,
            'default' => NULL,
          ),
        ),
        'primary_key' => 
        array (
          'api_keys_pkey' => 
          array (
            0 => 'api_key_id',
          ),
        ),
        'unique_keys' => 
        array (
        ),
        'foreign_keys' => 
        array (
          'api_keys_user_id_fkey' => 
          array (
            'columns' => 
            array (
              0 => 'user_id',
            ),
            'foreign_columns' => 
            array (
              0 => 'user_id',
            ),
            'table' => 'api_keys',
            'schema' => '',
            'foreign_table' => 'users',
            'foreign_schema' => '',
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
          ),
          'role_name' => 
          array (
            'name' => 'role_name',
            'type' => 'string',
            'nulls' => true,
            'default' => NULL,
          ),
        ),
        'primary_key' => 
        array (
          'role_id_pk' => 
          array (
            0 => 'role_id',
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
      'audit_trail' => 
      array (
        'schema' => '',
        'name' => 'audit_trail',
        'columns' => 
        array (
          'audit_trail_id' => 
          array (
            'name' => 'audit_trail_id',
            'type' => 'integer',
            'nulls' => false,
          ),
          'user_id' => 
          array (
            'name' => 'user_id',
            'type' => 'integer',
            'nulls' => false,
            'default' => NULL,
          ),
          'item_id' => 
          array (
            'name' => 'item_id',
            'type' => 'integer',
            'nulls' => false,
            'default' => NULL,
          ),
          'item_type' => 
          array (
            'name' => 'item_type',
            'type' => 'string',
            'nulls' => false,
            'default' => NULL,
          ),
          'description' => 
          array (
            'name' => 'description',
            'type' => 'string',
            'nulls' => false,
            'default' => NULL,
          ),
          'audit_date' => 
          array (
            'name' => 'audit_date',
            'type' => 'timestamp',
            'nulls' => false,
            'default' => NULL,
          ),
          'type' => 
          array (
            'name' => 'type',
            'type' => 'double',
            'nulls' => false,
            'default' => NULL,
          ),
          'data' => 
          array (
            'name' => 'data',
            'type' => 'text',
            'nulls' => true,
            'default' => NULL,
          ),
        ),
        'primary_key' => 
        array (
          'audit_trail_audit_id_pk' => 
          array (
            0 => 'audit_trail_id',
          ),
        ),
        'unique_keys' => 
        array (
        ),
        'foreign_keys' => 
        array (
          'audit_trail_user_id_fk' => 
          array (
            'columns' => 
            array (
              0 => 'user_id',
            ),
            'foreign_columns' => 
            array (
              0 => 'user_id',
            ),
            'table' => 'audit_trail',
            'schema' => '',
            'foreign_table' => 'users',
            'foreign_schema' => '',
          ),
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
          'permission_id' => 
          array (
            'name' => 'permission_id',
            'type' => 'integer',
            'nulls' => false,
          ),
          'role_id' => 
          array (
            'name' => 'role_id',
            'type' => 'integer',
            'nulls' => false,
            'default' => NULL,
          ),
          'permission' => 
          array (
            'name' => 'permission',
            'type' => 'string',
            'nulls' => true,
            'default' => NULL,
          ),
          'value' => 
          array (
            'name' => 'value',
            'type' => 'double',
            'nulls' => false,
            'default' => NULL,
          ),
          'module' => 
          array (
            'name' => 'module',
            'type' => 'string',
            'nulls' => true,
            'default' => NULL,
          ),
        ),
        'primary_key' => 
        array (
          'perm_id_pk' => 
          array (
            0 => 'permission_id',
          ),
        ),
        'unique_keys' => 
        array (
        ),
        'foreign_keys' => 
        array (
          'permissios_role_id_fk' => 
          array (
            'columns' => 
            array (
              0 => 'role_id',
            ),
            'foreign_columns' => 
            array (
              0 => 'role_id',
            ),
            'table' => 'permissions',
            'schema' => '',
            'foreign_table' => 'roles',
            'foreign_schema' => '',
          ),
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
          'user_id' => 
          array (
            'name' => 'user_id',
            'type' => 'integer',
            'nulls' => false,
          ),
          'user_name' => 
          array (
            'name' => 'user_name',
            'type' => 'string',
            'nulls' => false,
            'default' => NULL,
          ),
          'password' => 
          array (
            'name' => 'password',
            'type' => 'string',
            'nulls' => false,
            'default' => NULL,
          ),
          'role_id' => 
          array (
            'name' => 'role_id',
            'type' => 'integer',
            'nulls' => true,
            'default' => NULL,
          ),
          'first_name' => 
          array (
            'name' => 'first_name',
            'type' => 'string',
            'nulls' => false,
            'default' => NULL,
          ),
          'last_name' => 
          array (
            'name' => 'last_name',
            'type' => 'string',
            'nulls' => false,
            'default' => NULL,
          ),
          'other_names' => 
          array (
            'name' => 'other_names',
            'type' => 'string',
            'nulls' => true,
            'default' => NULL,
          ),
          'user_status' => 
          array (
            'name' => 'user_status',
            'type' => 'double',
            'nulls' => true,
            'default' => NULL,
          ),
          'email' => 
          array (
            'name' => 'email',
            'type' => 'string',
            'nulls' => false,
            'default' => NULL,
          ),
        ),
        'primary_key' => 
        array (
          'user_id_pk' => 
          array (
            0 => 'user_id',
          ),
        ),
        'unique_keys' => 
        array (
          'user_name_uk' => 
          array (
            0 => 'user_name',
          ),
        ),
        'foreign_keys' => 
        array (
          'users_role_id_fk' => 
          array (
            'columns' => 
            array (
              0 => 'role_id',
            ),
            'foreign_columns' => 
            array (
              0 => 'role_id',
            ),
            'table' => 'users',
            'schema' => '',
            'foreign_table' => 'roles',
            'foreign_schema' => '',
          ),
        ),
        'indices' => 
        array (
        ),
        'auto_increment' => true,
      ),
      'yentu_history' => 
      array (
        'schema' => '',
        'name' => 'yentu_history',
        'columns' => 
        array (
          'session' => 
          array (
            'name' => 'session',
            'type' => 'string',
            'nulls' => true,
            'default' => NULL,
          ),
          'version' => 
          array (
            'name' => 'version',
            'type' => 'string',
            'nulls' => true,
            'default' => NULL,
          ),
          'method' => 
          array (
            'name' => 'method',
            'type' => 'string',
            'nulls' => true,
            'default' => NULL,
          ),
          'arguments' => 
          array (
            'name' => 'arguments',
            'type' => 'text',
            'nulls' => true,
            'default' => NULL,
          ),
          'migration' => 
          array (
            'name' => 'migration',
            'type' => 'string',
            'nulls' => true,
            'default' => NULL,
          ),
          'id' => 
          array (
            'name' => 'id',
            'type' => 'integer',
            'nulls' => false,
          ),
        ),
        'primary_key' => 
        array (
          'yentu_history_pk' => 
          array (
            0 => 'id',
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
    ),
  );