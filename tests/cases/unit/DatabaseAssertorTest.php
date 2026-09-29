<?php

namespace yentu\tests\cases\unit;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use yentu\DatabaseAssertor;

#[Group('unit')]
class DatabaseAssertorTest extends TestCase
{
    private function createSampleDescription(): array
    {
        return [
            'schemata' => [
                'custom_schema' => [
                    'tables' => [
                        'orders' => [
                            'name' => 'orders',
                            'columns' => [
                                'id' => ['type' => 'integer', 'nulls' => false],
                                'total' => ['type' => 'double', 'nulls' => true],
                            ],
                            'primary_key' => [
                                'orders_pk' => ['columns' => ['id']]
                            ],
                            'flat_primary_key' => [
                                'id' => true,
                            ],
                            'unique_keys' => [],
                            'flat_unique_keys' => [],
                            'foreign_keys' => [],
                            'flat_foreign_keys' => [],
                            'indices' => [],
                            'flat_indices' => [],
                        ]
                    ],
                    'views' => [
                        'orders_view' => [
                            'definition' => 'SELECT id, total FROM orders',
                        ]
                    ]
                ]
            ],
            'tables' => [
                'users' => [
                    'name' => 'users',
                    'columns' => [
                        'id' => ['type' => 'integer', 'nulls' => false],
                        'username' => ['type' => 'string', 'nulls' => false],
                        'role_id' => ['type' => 'integer', 'nulls' => true],
                    ],
                    'primary_key' => [
                        'users_pk' => ['columns' => ['id']]
                    ],
                    'flat_primary_key' => [
                        'id' => true,
                    ],
                    'unique_keys' => [
                        'users_username_uk' => ['columns' => ['username']]
                    ],
                    'flat_unique_keys' => [
                        'username' => true,
                    ],
                    'foreign_keys' => [
                        'users_roles_fk' => [
                            'columns' => ['role_id'],
                            'foreign_table' => 'roles',
                            'foreign_columns' => ['id']
                        ]
                    ],
                    'flat_foreign_keys' => [
                        'role_id' => true,
                    ],
                    'indices' => [
                        'users_role_idx' => ['columns' => ['role_id']]
                    ],
                    'flat_indices' => [
                        'role_id' => true,
                    ],
                ]
            ],
            'views' => [
                'users_view' => [
                    'definition' => 'SELECT id, username FROM users',
                ]
            ]
        ];
    }

    public function testDoesSchemaExist(): void
    {
        $assertor = new DatabaseAssertor($this->createSampleDescription());

        $this->assertTrue($assertor->doesSchemaExist('custom_schema'));
        $this->assertFalse($assertor->doesSchemaExist('nonexistent_schema'));
    }

    public function testDoesTableExistDefaultAndCustomSchema(): void
    {
        $assertor = new DatabaseAssertor($this->createSampleDescription());

        $this->assertTrue($assertor->doesTableExist('users'));
        $this->assertTrue($assertor->doesTableExist(['schema' => false, 'name' => 'users']));
        $this->assertFalse($assertor->doesTableExist('orders'));

        $this->assertTrue($assertor->doesTableExist(['schema' => 'custom_schema', 'name' => 'orders']));
        $this->assertFalse($assertor->doesTableExist(['schema' => 'custom_schema', 'name' => 'nonexistent']));
    }

    public function testDoesColumnExist(): void
    {
        $assertor = new DatabaseAssertor($this->createSampleDescription());

        $column = $assertor->doesColumnExist(['schema' => false, 'table' => 'users', 'name' => 'username']);
        $this->assertNotFalse($column);
        $this->assertEquals('string', $column['type']);

        $this->assertFalse($assertor->doesColumnExist(['schema' => false, 'table' => 'users', 'name' => 'email']));

        $orderCol = $assertor->doesColumnExist(['schema' => 'custom_schema', 'table' => 'orders', 'name' => 'total']);
        $this->assertNotFalse($orderCol);
    }

    public function testDoesPrimaryKeyExist(): void
    {
        $assertor = new DatabaseAssertor($this->createSampleDescription());

        $this->assertNotFalse($assertor->doesPrimaryKeyExist(['schema' => false, 'table' => 'users', 'name' => 'users_pk']));
        $this->assertTrue($assertor->doesPrimaryKeyExist(['schema' => false, 'table' => 'users', 'columns' => ['id']]));
        $this->assertFalse($assertor->doesPrimaryKeyExist(['schema' => false, 'table' => 'users', 'name' => 'unknown_pk']));
    }

    public function testDoesUniqueKeyExist(): void
    {
        $assertor = new DatabaseAssertor($this->createSampleDescription());

        $this->assertNotFalse($assertor->doesUniqueKeyExist(['schema' => false, 'table' => 'users', 'name' => 'users_username_uk']));
        $this->assertTrue($assertor->doesUniqueKeyExist(['schema' => false, 'table' => 'users', 'columns' => ['username']]));
        $this->assertFalse($assertor->doesUniqueKeyExist(['schema' => false, 'table' => 'users', 'columns' => ['role_id']]));
    }

    public function testDoesForeignKeyExist(): void
    {
        $assertor = new DatabaseAssertor($this->createSampleDescription());

        $this->assertNotFalse($assertor->doesForeignKeyExist(['schema' => false, 'table' => 'users', 'name' => 'users_roles_fk']));
        $this->assertTrue($assertor->doesForeignKeyExist(['schema' => false, 'table' => 'users', 'columns' => ['role_id']]));
        $this->assertFalse($assertor->doesForeignKeyExist(['schema' => false, 'table' => 'users', 'name' => 'wrong_fk']));
    }

    public function testDoesIndexExist(): void
    {
        $assertor = new DatabaseAssertor($this->createSampleDescription());

        $this->assertNotFalse($assertor->doesIndexExist(['schema' => false, 'table' => 'users', 'name' => 'users_role_idx']));
        $this->assertTrue($assertor->doesIndexExist(['schema' => false, 'table' => 'users', 'columns' => ['role_id']]));
        $this->assertFalse($assertor->doesIndexExist(['schema' => false, 'table' => 'users', 'name' => 'wrong_idx']));
    }

    public function testDoesViewExist(): void
    {
        $assertor = new DatabaseAssertor($this->createSampleDescription());

        $def = $assertor->doesViewExist('users_view');
        $this->assertEquals('SELECT id, username FROM users', $def);

        $customDef = $assertor->doesViewExist(['schema' => 'custom_schema', 'name' => 'orders_view']);
        $this->assertEquals('SELECT id, total FROM orders', $customDef);

        $this->assertFalse($assertor->doesViewExist('nonexistent_view'));
    }
}
