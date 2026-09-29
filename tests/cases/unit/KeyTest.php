<?php

namespace yentu\tests\cases\unit;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use yentu\ChangeLogger;
use yentu\database\ForeignKey;
use yentu\database\Index;
use yentu\database\PrimaryKey;
use yentu\database\Schema;
use yentu\database\Table;
use yentu\database\UniqueKey;
use yentu\exceptions\DatabaseManipulatorException;
use yentu\exceptions\SyntaxErrorException;

#[Group('unit')]
class KeyTest extends TestCase
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

    public function testPrimaryKeyBuildDescriptionAndCommit(): void
    {
        $table = $this->createTableStub('users', 'public');
        $logger = $this->createMock(ChangeLogger::class);

        $logger->expects($this->exactly(2))
            ->method('__call')
            ->willReturnCallback(function ($method, $args) {
                if ($method === 'doesPrimaryKeyExist') {
                    $this->assertEquals('users_id_pk', $args[0]['name']);
                    return false;
                }
                if ($method === 'addPrimaryKey') {
                    $this->assertEquals([
                        'table' => 'users',
                        'schema' => 'public',
                        'columns' => ['id'],
                        'name' => 'users_id_pk',
                    ], $args[0]);
                    return true;
                }
                return null;
            });

        $pk = new PrimaryKey(['id'], $table);
        $pk->setChangeLogger($logger);

        $this->assertTrue($pk->isNew());
        $pk->commit();
    }

    public function testPrimaryKeyCustomNameAndDrop(): void
    {
        $table = $this->createTableStub('users', 'public');
        $logger = $this->createMock(ChangeLogger::class);

        $logger->expects($this->exactly(2))
            ->method('__call')
            ->willReturnCallback(function ($method, $args) {
                if ($method === 'doesPrimaryKeyExist') {
                    $this->assertEquals('pk_users_id', $args[0]['name']);
                    return true;
                }
                if ($method === 'dropPrimaryKey') {
                    $this->assertEquals('pk_users_id', $args[0]['name']);
                    return true;
                }
                return null;
            });

        $pk = new PrimaryKey(['id'], $table);
        $pk->setChangeLogger($logger);
        $pk->name('pk_users_id');

        $this->assertFalse($pk->isNew());
        $pk->drop();
    }

    public function testPrimaryKeyAutoIncrement(): void
    {
        $table = $this->createTableStub('users', 'public');
        $logger = $this->createMock(ChangeLogger::class);

        $logger->expects($this->once())
            ->method('__call')
            ->with('addAutoPrimaryKey', $this->callback(function ($args) {
                return $args[0]['table'] === 'users'
                    && $args[0]['schema'] === 'public'
                    && $args[0]['column'] === 'id';
            }))
            ->willReturn(true);

        $pk = new PrimaryKey(['id'], $table);
        $pk->setChangeLogger($logger);
        $pk->autoIncrement();
    }

    public function testPrimaryKeyAutoIncrementThrowsOnCompositeKey(): void
    {
        $table = $this->createTableStub('user_roles', 'public');
        $pk = new PrimaryKey(['user_id', 'role_id'], $table);

        $this->expectException(SyntaxErrorException::class);
        $this->expectExceptionMessage('Cannot make an auto incrementing composite key.');
        $pk->autoIncrement();
    }

    public function testUniqueKeyBuildDescriptionAndCommit(): void
    {
        $table = $this->createTableStub('users', 'public');
        $logger = $this->createMock(ChangeLogger::class);

        $logger->expects($this->exactly(2))
            ->method('__call')
            ->willReturnCallback(function ($method, $args) {
                if ($method === 'doesUniqueKeyExist') {
                    $this->assertEquals('users_email_uk', $args[0]['name']);
                    return false;
                }
                if ($method === 'addUniqueKey') {
                    $this->assertEquals([
                        'table' => 'users',
                        'schema' => 'public',
                        'columns' => ['email'],
                        'name' => 'users_email_uk',
                    ], $args[0]);
                    return true;
                }
                return null;
            });

        $uk = new UniqueKey(['email'], $table);
        $uk->setChangeLogger($logger);

        $this->assertTrue($uk->isNew());
        $uk->commit();
    }

    public function testIndexUniqueAndCommit(): void
    {
        $table = $this->createTableStub('users', 'public');
        $logger = $this->createMock(ChangeLogger::class);

        $logger->expects($this->exactly(2))
            ->method('__call')
            ->willReturnCallback(function ($method, $args) {
                if ($method === 'doesIndexExist') {
                    $this->assertEquals('users_email_idx', $args[0]['name']);
                    return false;
                }
                if ($method === 'addIndex') {
                    $this->assertEquals([
                        'table' => 'users',
                        'schema' => 'public',
                        'columns' => ['email'],
                        'name' => 'users_email_idx',
                        'unique' => true,
                    ], $args[0]);
                    return true;
                }
                return null;
            });

        $index = new Index(['email'], $table);
        $index->setChangeLogger($logger);
        $index->unique(true);

        $this->assertTrue($index->isNew());
        $index->commit();
    }

    public function testForeignKeyNonReferencingTableThrowsException(): void
    {
        $table = $this->createTableStub('posts', 'public');
        $foreignTable = $this->createStub(Table::class);
        $foreignTable->method('isReference')->willReturn(false);

        $fk = new ForeignKey(['author_id'], $table);

        $this->expectException(DatabaseManipulatorException::class);
        $this->expectExceptionMessage('References cannot be created from a non referencing table.');
        $fk->references($foreignTable);
    }

    public function testForeignKeyCommitNewSuccess(): void
    {
        $table = $this->createTableStub('posts', 'public');

        $foreignSchema = $this->createStub(Schema::class);
        $foreignSchema->method('getName')->willReturn('public');

        $foreignTable = $this->createStub(Table::class);
        $foreignTable->method('getName')->willReturn('users');
        $foreignTable->method('getSchema')->willReturn($foreignSchema);
        $foreignTable->method('isReference')->willReturn(true);

        $logger = $this->createMock(ChangeLogger::class);
        $logger->expects($this->exactly(2))
            ->method('__call')
            ->willReturnCallback(function ($method, $args) {
                if ($method === 'doesForeignKeyExist') {
                    return false;
                }
                if ($method === 'addForeignKey') {
                    $this->assertEquals([
                        'columns' => ['author_id'],
                        'table' => 'posts',
                        'schema' => 'public',
                        'foreign_columns' => ['id'],
                        'foreign_table' => 'users',
                        'foreign_schema' => 'public',
                        'name' => 'posts_author_id_users_id_fk',
                        'on_delete' => 'CASCADE',
                        'on_update' => 'NO ACTION',
                    ], $args[0]);
                    return true;
                }
                return null;
            });

        $fk = new ForeignKey(['author_id'], $table);
        $fk->setChangeLogger($logger);
        $fk->init();

        $this->assertTrue($fk->isNew());

        $fk->references($foreignTable)
            ->columns('id')
            ->onDelete('CASCADE');

        $fk->commit();
    }
}
