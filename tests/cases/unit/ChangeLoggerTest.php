<?php

namespace yentu\tests\cases\unit;

use clearice\io\Io;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use yentu\ChangeLogger;
use yentu\manipulators\AbstractDatabaseManipulator;
use yentu\Migrations;

#[Group('unit')]
class ChangeLoggerTest extends TestCase
{
    public function testWrapCallsCreateHistoryOnDriver(): void
    {
        $driver = $this->createMock(AbstractDatabaseManipulator::class);
        $driver->expects($this->once())->method('createHistory');

        $migrations = $this->createStub(Migrations::class);
        $io = $this->createStub(Io::class);

        $logger = ChangeLogger::wrap($driver, $migrations, $io);
        $this->assertInstanceOf(ChangeLogger::class, $logger);
    }

    public function testPerformOperationCallsDriverAnnouncesAndLogsToHistory(): void
    {
        $driver = $this->createMock(AbstractDatabaseManipulator::class);
        $driver->expects($this->once())->method('createHistory');
        $driver->expects($this->once())
            ->method('__call')
            ->with('addTable', [['name' => 'users', 'schema' => 'public']])
            ->willReturn(true);

        $driver->expects($this->once())
            ->method('query')
            ->with($this->stringContains('INSERT INTO yentu_history'), $this->callback(function ($params) {
                return $params[1] === '20260101000000' &&
                    $params[2] === 'addTable' &&
                    $params[4] === 'create_users';
            }));

        $migrations = $this->createMock(Migrations::class);
        $migrations->expects($this->once())
            ->method('announce')
            ->with('add', 'Table', ['name' => 'users', 'schema' => 'public']);

        $io = $this->createStub(Io::class);

        $logger = ChangeLogger::wrap($driver, $migrations, $io);
        $logger->setVersion('20260101000000');
        $logger->setMigration('create_users');

        $result = $logger->addTable(['name' => 'users', 'schema' => 'public']);
        $this->assertTrue($result);
    }

    public function testSkipItemTypeDoesNotCallDriver(): void
    {
        $driver = $this->createMock(AbstractDatabaseManipulator::class);
        $driver->expects($this->once())->method('createHistory');
        $driver->expects($this->never())->method('__call');

        $migrations = $this->createStub(Migrations::class);
        $output = '';
        $io = $this->createMock(Io::class);
        $io->expects($this->atLeastOnce())
            ->method('output')
            ->willReturnCallback(function ($text) use (&$output) {
                $output .= $text;
            });

        $logger = ChangeLogger::wrap($driver, $migrations, $io);
        $logger->skip('Table');

        $logger->addTable(['name' => 'users']);
        $this->assertStringContainsString("Skipping Table 'users'", $output);
    }
}
