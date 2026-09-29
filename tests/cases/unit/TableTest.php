<?php

namespace yentu\tests\cases\unit;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use yentu\ChangeLogger;
use yentu\database\Column;
use yentu\database\ItemType;
use yentu\database\PrimaryKey;
use yentu\database\Schema;
use yentu\database\Table;
use yentu\factories\DatabaseItemFactory;
use yentu\SchemaDescription;

#[Group('unit')]
class TableTest extends TestCase
{
    public function testTableInitAddsTableWhenDoesNotExist(): void
    {
        $driver = $this->createMock(ChangeLogger::class);
        $driver->expects($this->exactly(2))
            ->method('__call')
            ->willReturnCallback(function ($method, $args) {
                if ($method === 'doesTableExist') {
                    return false;
                }
                if ($method === 'addTable') {
                    $this->assertEquals(['name' => 'users', 'schema' => 'public'], $args[0]);
                    return true;
                }
                return null;
            });

        $schema = $this->createStub(Schema::class);
        $schema->method('getName')->willReturn('public');

        $table = new Table('users', $schema);
        $table->setChangeLogger($driver);
        $table->init();

        $this->assertEquals('users', $table->getName());
        $this->assertSame($schema, $table->getSchema());
    }

    public function testTableInitDoesNotAddTableWhenAlreadyExists(): void
    {
        $driver = $this->createMock(ChangeLogger::class);
        $driver->expects($this->once())
            ->method('__call')
            ->with('doesTableExist', $this->callback(function ($args) {
                return $args[0]['name'] === 'users';
            }))
            ->willReturn(true);

        $schema = $this->createStub(Schema::class);
        $schema->method('getName')->willReturn('public');

        $table = new Table('users', $schema);
        $table->setChangeLogger($driver);
        $table->init();

        $this->assertEquals('users', $table->getName());
    }

    public function testTableColumnDelegatesToFactory(): void
    {
        $schema = $this->createStub(Schema::class);
        $schema->method('getName')->willReturn('public');

        $table = new Table('users', $schema);

        $column = $this->createStub(Column::class);
        $factory = $this->createMock(DatabaseItemFactory::class);
        $factory->expects($this->once())
            ->method('create')
            ->with(ItemType::Column, 'email', $table)
            ->willReturn($column);

        $table->setFactory($factory);

        $result = $table->column('email');
        $this->assertSame($column, $result);
    }

    public function testTablePrimaryKeyDelegatesToFactory(): void
    {
        $schema = $this->createStub(Schema::class);
        $schema->method('getName')->willReturn('public');

        $table = new Table('users', $schema);

        $pk = $this->createStub(PrimaryKey::class);
        $factory = $this->createMock(DatabaseItemFactory::class);
        $factory->expects($this->once())
            ->method('create')
            ->with(ItemType::PrimaryKey, ['id'], $table)
            ->willReturn($pk);

        $table->setFactory($factory);

        $result = $table->primaryKey('id');
        $this->assertSame($pk, $result);
    }

    public function testTableDropCallsDropTableOnDriver(): void
    {
        $schemaDesc = $this->createStub(SchemaDescription::class);
        $schemaDesc->method('getTable')
            ->willReturn(['name' => 'users', 'schema' => 'public', 'columns' => []]);

        $driver = $this->createMock(ChangeLogger::class);
        $driver->expects($this->exactly(2))
            ->method('__call')
            ->willReturnCallback(function ($method, $args) use ($schemaDesc) {
                if ($method === 'getDescription') {
                    return $schemaDesc;
                }
                if ($method === 'dropTable') {
                    $this->assertEquals(['name' => 'users', 'schema' => 'public', 'columns' => []], $args[0]);
                    return true;
                }
                return null;
            });

        $schema = $this->createStub(Schema::class);
        $schema->method('getName')->willReturn('public');

        $table = new Table('users', $schema);
        $table->setChangeLogger($driver);

        $table->drop();
    }
}
