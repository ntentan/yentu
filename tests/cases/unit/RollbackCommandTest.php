<?php

namespace yentu\tests\cases\unit;

use clearice\io\Io;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use yentu\ChangeReverser;
use yentu\commands\Rollback;
use yentu\factories\DatabaseManipulatorFactory;
use yentu\manipulators\AbstractDatabaseManipulator;

#[Group('unit')]
class RollbackCommandTest extends TestCase
{
    public function testRollbackLastSessionReversesOperationsAndDeletesFromHistory(): void
    {
        $db = $this->createMock(AbstractDatabaseManipulator::class);
        $db->method('getLastSession')->willReturn('test_session_abc');

        $operations = [
            [
                'id' => 10,
                'method' => 'addTable',
                'arguments' => json_encode([['name' => 'users', 'schema' => 'public']]),
                'migration' => 'create_users_table',
                'default_schema' => 'public',
            ]
        ];

        $db->expects($this->exactly(2))
            ->method('query')
            ->willReturnCallback(function ($query, $bind) use ($operations) {
                if (str_starts_with($query, 'SELECT')) {
                    return $operations;
                }
                if (str_starts_with($query, 'DELETE')) {
                    $this->assertEquals([10], $bind);
                    return [];
                }
                return [];
            });

        $factory = $this->createStub(DatabaseManipulatorFactory::class);
        $factory->method('createManipulator')->willReturn($db);

        $reverser = $this->createMock(ChangeReverser::class);
        $reverser->expects($this->once())->method('setDriver')->with($db);
        $reverser->expects($this->once())
            ->method('call')
            ->with('addTable', [['name' => 'users', 'schema' => 'public']]);

        $io = $this->createMock(Io::class);
        $io->expects($this->once())
            ->method('output')
            ->with("Rolling back 'create_users_table' migration on `public` schema.\n");

        $rollback = new Rollback($factory, $io, $reverser);
        $rollback->run();
    }

    public function testRollbackWithSpecificVersionArgument(): void
    {
        $db = $this->createMock(AbstractDatabaseManipulator::class);

        $operations = [
            [
                'id' => 1,
                'method' => 'addColumn',
                'arguments' => json_encode([['name' => 'email', 'table' => 'users']]),
                'migration' => 'add_email_column',
                'default_schema' => '',
            ]
        ];

        $db->expects($this->exactly(2))
            ->method('query')
            ->willReturnCallback(function ($query, $bind) use ($operations) {
                if (str_starts_with($query, 'SELECT')) {
                    $this->assertStringContainsString('version = ?', $query);
                    return $operations;
                }
                if (str_starts_with($query, 'DELETE')) {
                    $this->assertEquals([1], $bind);
                    return [];
                }
                return [];
            });

        $factory = $this->createStub(DatabaseManipulatorFactory::class);
        $factory->method('createManipulator')->willReturn($db);

        $reverser = $this->createMock(ChangeReverser::class);
        $reverser->expects($this->once())->method('call');

        $io = $this->createMock(Io::class);
        $io->expects($this->once())
            ->method('output')
            ->with("Rolling back 'add_email_column' migration.\n");

        $rollback = new Rollback($factory, $io, $reverser);
        $rollback->setOptions(['__args' => ['20260101120000']]);
        $rollback->run();
    }

    public function testRollbackWithSpecificMigrationNameArgument(): void
    {
        $db = $this->createMock(AbstractDatabaseManipulator::class);

        $operations = [
            [
                'id' => 5,
                'method' => 'dropTable',
                'arguments' => json_encode([['name' => 'temp']]),
                'migration' => 'drop_temp_table',
                'default_schema' => '',
            ]
        ];

        $db->expects($this->exactly(2))
            ->method('query')
            ->willReturnCallback(function ($query, $bind) use ($operations) {
                if (str_starts_with($query, 'SELECT')) {
                    $this->assertStringContainsString('migration = ?', $query);
                    return $operations;
                }
                return [];
            });

        $factory = $this->createStub(DatabaseManipulatorFactory::class);
        $factory->method('createManipulator')->willReturn($db);

        $reverser = $this->createMock(ChangeReverser::class);
        $reverser->expects($this->once())->method('call');

        $io = $this->createStub(Io::class);

        $rollback = new Rollback($factory, $io, $reverser);
        $rollback->setOptions(['__args' => ['drop_temp_table']]);
        $rollback->run();
    }
}
