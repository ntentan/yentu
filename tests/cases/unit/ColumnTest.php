<?php

namespace yentu\tests\cases\unit;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use yentu\ChangeLogger;
use yentu\database\Column;
use yentu\database\Schema;
use yentu\database\Table;

#[Group('unit')]
class ColumnTest extends TestCase
{
    private function createTableStub(string $tableName = 'users', string $schemaName = 'public'): Table
    {
        $schema = $this->createStub(Schema::class);
        $schema->method('getName')->willReturn($schemaName);

        $table = $this->createStub(Table::class);
        $table->method('getName')->willReturn($tableName);
        $table->method('getSchema')->willReturn($schema);

        return $table;
    }

    public function testNewColumnInitializationAndCommit(): void
    {
        $table = $this->createTableStub('users', 'public');
        $logger = $this->createMock(ChangeLogger::class);

        $logger->expects($this->exactly(2))
            ->method('__call')
            ->willReturnCallback(function ($method, $args) {
                if ($method === 'doesColumnExist') {
                    $this->assertEquals([
                        'table' => 'users',
                        'schema' => 'public',
                        'name' => 'email',
                    ], $args[0]);
                    return false;
                }
                if ($method === 'addColumn') {
                    $this->assertEquals([
                        'name' => 'email',
                        'type' => 'string',
                        'table' => 'users',
                        'schema' => 'public',
                        'nulls' => false,
                        'length' => 255,
                        'default' => 'guest@example.com',
                    ], $args[0]);
                    return true;
                }
                return null;
            });

        $column = new Column('email', $table);
        $column->setChangeLogger($logger);
        $column->init();

        $this->assertTrue($column->isNew());

        $column->type('string')
            ->length(255)
            ->nulls(false)
            ->defaultValue('guest@example.com');

        $column->commit();
        $this->assertFalse($column->isNew());
    }

    public function testExistingColumnModificationAndCommit(): void
    {
        $table = $this->createTableStub('users', 'public');
        $logger = $this->createMock(ChangeLogger::class);

        $logger->expects($this->exactly(3))
            ->method('__call')
            ->willReturnCallback(function ($method, $args) {
                if ($method === 'doesColumnExist') {
                    return [
                        'default' => 'foo',
                        'length' => 100,
                        'type' => 'string',
                        'nulls' => true,
                    ];
                }
                if ($method === 'changeColumnnulls') {
                    $this->assertTrue($args[0]['from']['nulls']);
                    $this->assertFalse($args[0]['to']['nulls']);
                    return true;
                }
                if ($method === 'changeColumndefault') {
                    $this->assertEquals('foo', $args[0]['from']['default']);
                    $this->assertEquals('bar', $args[0]['to']['default']);
                    return true;
                }
                return null;
            });

        $column = new Column('username', $table);
        $column->setChangeLogger($logger);
        $column->init();

        $this->assertFalse($column->isNew());

        $column->nulls(false);
        $column->defaultValue('bar');
        $column->commit();
    }

    public function testColumnRequiredSetsNullsToFalse(): void
    {
        $table = $this->createTableStub('users', 'public');
        $logger = $this->createMock(ChangeLogger::class);

        $logger->expects($this->exactly(2))
            ->method('__call')
            ->willReturnCallback(function ($method, $args) {
                if ($method === 'doesColumnExist') {
                    return [
                        'default' => null,
                        'length' => 50,
                        'type' => 'string',
                        'nulls' => true,
                    ];
                }
                if ($method === 'changeColumnnulls') {
                    $this->assertFalse($args[0]['to']['nulls']);
                    return true;
                }
                return null;
            });

        $column = new Column('name', $table);
        $column->setChangeLogger($logger);
        $column->init();

        $column->required(true);
        $column->commit();
    }

    public function testColumnDrop(): void
    {
        $table = $this->createTableStub('users', 'public');
        $logger = $this->createMock(ChangeLogger::class);

        $logger->expects($this->once())
            ->method('__call')
            ->with('dropColumn', $this->callback(function ($args) {
                return $args[0]['name'] === 'bio'
                    && $args[0]['table'] === 'users'
                    && $args[0]['schema'] === 'public';
            }))
            ->willReturn(true);

        $column = new Column('bio', $table);
        $column->setChangeLogger($logger);

        $column->drop();
    }

    public function testColumnRename(): void
    {
        $table = $this->createTableStub('users', 'public');
        $logger = $this->createMock(ChangeLogger::class);

        $logger->expects($this->exactly(2))
            ->method('__call')
            ->willReturnCallback(function ($method, $args) {
                if ($method === 'doesColumnExist') {
                    return [
                        'default' => null,
                        'length' => 50,
                        'type' => 'string',
                        'nulls' => true,
                    ];
                }
                if ($method === 'changeColumnname') {
                    $this->assertEquals('name', $args[0]['from']['name']);
                    $this->assertEquals('full_name', $args[0]['to']['name']);
                    return true;
                }
                return null;
            });

        $column = new Column('name', $table);
        $column->setChangeLogger($logger);
        $column->init();

        $column->rename('full_name');
        $column->commit();
    }
}
