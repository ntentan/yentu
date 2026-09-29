<?php

namespace yentu\tests\cases\unit;

use clearice\io\Io;
use ntentan\utils\Filesystem;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use yentu\commands\Init;
use yentu\DatabaseAssertor;
use yentu\exceptions\CommandException;
use yentu\exceptions\NonReversibleCommandException;
use yentu\factories\DatabaseManipulatorFactory;
use yentu\manipulators\AbstractDatabaseManipulator;
use yentu\Migrations;

#[Group('unit')]
class InitCommandTest extends TestCase
{
    private string $tempDir;
    private string $yentuHome;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/yentu_init_test_' . uniqid();
        $this->yentuHome = $this->tempDir . '/yentu';
        mkdir($this->tempDir, 0777, true);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempDir)) {
            Filesystem::directory($this->tempDir)->delete();
        }
    }

    private function createMigrationsStub(): Migrations
    {
        $migrations = $this->createStub(Migrations::class);
        $migrations->method('getPath')
            ->willReturnCallback(function ($path) {
                return $this->yentuHome . ($path ? '/' . $path : '');
            });
        return $migrations;
    }

    public function testInitThrowsWhenAlreadyInitialized(): void
    {
        mkdir($this->yentuHome, 0777, true);

        $migrations = $this->createMigrationsStub();
        $factory = $this->createStub(DatabaseManipulatorFactory::class);
        $io = $this->createStub(Io::class);

        $command = new Init($migrations, $factory, $io);

        $this->expectException(NonReversibleCommandException::class);
        $this->expectExceptionMessage("Your project has already been initialized with yentu.");

        $command->run();
    }

    public function testInitThrowsWhenNoParametersProvided(): void
    {
        $migrations = $this->createMigrationsStub();
        $factory = $this->createStub(DatabaseManipulatorFactory::class);
        $io = $this->createStub(Io::class);

        $command = new Init($migrations, $factory, $io);
        $command->setOptions([]);

        $this->expectException(NonReversibleCommandException::class);
        $this->expectExceptionMessage("You didn't provide any parameters for initialization.");

        $command->run();
    }

    public function testInitThrowsWhenHistoryTableAlreadyExists(): void
    {
        $migrations = $this->createMigrationsStub();
        $factory = $this->createMock(DatabaseManipulatorFactory::class);
        $io = $this->createStub(Io::class);

        $assertor = $this->createMock(DatabaseAssertor::class);
        $assertor->expects($this->once())
            ->method('doesTableExist')
            ->with('yentu_history')
            ->willReturn(true);

        $manipulator = $this->createMock(AbstractDatabaseManipulator::class);
        $manipulator->expects($this->once())
            ->method('getAssertor')
            ->willReturn($assertor);

        $factory->expects($this->once())
            ->method('createManipulatorWithConfig')
            ->willReturn($manipulator);

        $command = new Init($migrations, $factory, $io);
        $command->setOptions([
            'driver' => 'sqlite',
            'file' => 'test.db',
        ]);

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("the 'yentu_history' table already exists.");

        $command->run();
    }

    public function testSuccessfulInitWithOptions(): void
    {
        $migrations = $this->createMigrationsStub();
        $factory = $this->createMock(DatabaseManipulatorFactory::class);
        $io = $this->createMock(Io::class);

        $assertor = $this->createMock(DatabaseAssertor::class);
        $assertor->expects($this->once())
            ->method('doesTableExist')
            ->with('yentu_history')
            ->willReturn(false);

        $manipulator = $this->createMock(AbstractDatabaseManipulator::class);
        $manipulator->expects($this->once())
            ->method('getAssertor')
            ->willReturn($assertor);
        $manipulator->expects($this->once())
            ->method('createHistory');
        $manipulator->expects($this->once())
            ->method('disconnect');

        $factory->expects($this->once())
            ->method('createManipulatorWithConfig')
            ->willReturn($manipulator);

        $io->expects($this->once())
            ->method('output')
            ->with("Yentu successfully initialized.\n");

        $command = new Init($migrations, $factory, $io);
        $command->setOptions([
            'driver' => 'sqlite',
            'file' => 'my_db.sqlite',
        ]);

        $command->run();

        $this->assertFileExists($this->yentuHome . '/config/yentu.ini');
        $this->assertDirectoryExists($this->yentuHome . '/migrations');
        $ini = parse_ini_file($this->yentuHome . '/config/yentu.ini', true);
        $this->assertEquals('sqlite', $ini['db']['driver']);
        $this->assertEquals('my_db.sqlite', $ini['db']['file']);
    }

    public function testSuccessfulInitInteractive(): void
    {
        $migrations = $this->createMigrationsStub();
        $factory = $this->createMock(DatabaseManipulatorFactory::class);
        $io = $this->createMock(Io::class);

        $io->expects($this->exactly(2))
            ->method('getResponse')
            ->willReturnCallback(function ($prompt) {
                if (str_contains($prompt, 'database are you working with')) {
                    return 'sqlite';
                }
                if (str_contains($prompt, 'path to your database file')) {
                    return 'interactive.db';
                }
                return '';
            });

        $assertor = $this->createMock(DatabaseAssertor::class);
        $assertor->expects($this->once())
            ->method('doesTableExist')
            ->with('yentu_history')
            ->willReturn(false);

        $manipulator = $this->createMock(AbstractDatabaseManipulator::class);
        $manipulator->expects($this->once())
            ->method('getAssertor')
            ->willReturn($assertor);
        $manipulator->expects($this->once())
            ->method('createHistory');
        $manipulator->expects($this->once())
            ->method('disconnect');

        $factory->expects($this->once())
            ->method('createManipulatorWithConfig')
            ->willReturn($manipulator);

        $io->expects($this->once())
            ->method('output')
            ->with("Yentu successfully initialized.\n");

        $command = new Init($migrations, $factory, $io);
        $command->setOptions(['interactive' => true]);

        $command->run();

        $this->assertFileExists($this->yentuHome . '/config/yentu.ini');
        $ini = parse_ini_file($this->yentuHome . '/config/yentu.ini', true);
        $this->assertEquals('sqlite', $ini['db']['driver']);
        $this->assertEquals('interactive.db', $ini['db']['file']);
    }

    public function testReverseActions(): void
    {
        mkdir($this->yentuHome, 0777, true);
        $this->assertDirectoryExists($this->yentuHome);

        $migrations = $this->createMigrationsStub();
        $factory = $this->createStub(DatabaseManipulatorFactory::class);
        $io = $this->createStub(Io::class);

        $command = new Init($migrations, $factory, $io);
        $command->reverseActions();

        $this->assertFileDoesNotExist($this->yentuHome);
    }
}
