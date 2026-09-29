<?php

namespace yentu\tests\cases\unit;

use clearice\io\Io;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use yentu\factories\DatabaseManipulatorFactory;
use yentu\manipulators\AbstractDatabaseManipulator;
use yentu\Migrations;

#[Group('unit')]
class MigrationsTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/yentu_migrations_test_' . uniqid();
        mkdir($this->tempDir . '/migrations', 0777, true);
    }

    protected function tearDown(): void
    {
        $files = glob($this->tempDir . '/migrations/*');
        foreach ($files as $file) {
            unlink($file);
        }
        if (is_dir($this->tempDir . '/migrations')) {
            rmdir($this->tempDir . '/migrations');
        }
        if (is_dir($this->tempDir)) {
            rmdir($this->tempDir);
        }
    }

    public function testGetPathWithDefaultAndCustomHome(): void
    {
        $io = $this->createStub(Io::class);
        $factory = $this->createStub(DatabaseManipulatorFactory::class);

        $defaultMigrations = new Migrations($io, $factory);
        $this->assertEquals('./yentu' . DIRECTORY_SEPARATOR . 'config', $defaultMigrations->getPath('config'));

        $customMigrations = new Migrations($io, $factory, ['home' => '/custom/path']);
        $this->assertEquals('/custom/path' . DIRECTORY_SEPARATOR . 'config', $customMigrations->getPath('config'));
    }

    public function testGetAllPaths(): void
    {
        $io = $this->createStub(Io::class);
        $factory = $this->createStub(DatabaseManipulatorFactory::class);

        $config = [
            'home' => '/app/yentu',
            'variables' => ['env' => 'test'],
            'other_migrations' => [
                ['home' => '/plugins/migrations', 'variables' => []]
            ]
        ];
        $migrations = new Migrations($io, $factory, $config);
        $paths = $migrations->getAllPaths();

        $this->assertCount(2, $paths);
        $this->assertEquals('/app/yentu' . DIRECTORY_SEPARATOR . 'migrations', $paths[0]['home']);
        $this->assertEquals(['env' => 'test'], $paths[0]['variables']);
        $this->assertEquals('/plugins/migrations', $paths[1]['home']);
    }

    public function testGetMigrationFilesParsesTimestampsAndNames(): void
    {
        $io = $this->createStub(Io::class);
        $factory = $this->createStub(DatabaseManipulatorFactory::class);

        touch($this->tempDir . '/migrations/20260101120000_create_users.php');
        touch($this->tempDir . '/migrations/20260102153000_add_roles_table.php');
        touch($this->tempDir . '/migrations/invalid_format.php');
        touch($this->tempDir . '/migrations/not_a_migration.txt');

        $migrations = new Migrations($io, $factory, ['home' => $this->tempDir]);
        $files = $migrations->getMigrationFiles($this->tempDir . '/migrations');

        $this->assertCount(2, $files);
        $this->assertArrayHasKey('20260101120000', $files);
        $this->assertEquals('create_users', $files['20260101120000']['migration']);
        $this->assertEquals('20260101120000_create_users.php', $files['20260101120000']['file']);

        $this->assertArrayHasKey('20260102153000', $files);
        $this->assertEquals('add_roles_table', $files['20260102153000']['migration']);
    }

    public function testGetMigrationFilesReturnsEmptyWhenPathDoesNotExist(): void
    {
        $io = $this->createStub(Io::class);
        $factory = $this->createStub(DatabaseManipulatorFactory::class);

        $migrations = new Migrations($io, $factory);
        $files = $migrations->getMigrationFiles('/non/existent/path');

        $this->assertEquals([], $files);
    }

    public function testGetRunMigrations(): void
    {
        $io = $this->createStub(Io::class);
        $factory = $this->createMock(DatabaseManipulatorFactory::class);
        $manipulator = $this->createMock(AbstractDatabaseManipulator::class);

        $manipulator->expects($this->once())
            ->method('query')
            ->with("SELECT DISTINCT version, migration, default_schema FROM yentu_history ORDER BY version")
            ->willReturn([
                ['version' => '20260101120000', 'migration' => 'create_users', 'default_schema' => 'public'],
                ['version' => '20260102153000', 'migration' => 'add_roles_table', 'default_schema' => 'public'],
            ]);

        $factory->expects($this->once())
            ->method('createManipulator')
            ->willReturn($manipulator);

        $migrations = new Migrations($io, $factory);
        $run = $migrations->getRunMirations();

        $this->assertCount(2, $run);
        $this->assertEquals('create_users', $run['20260101120000']['migration']);
        $this->assertEquals('public', $run['20260101120000']['default_schema']);
    }

    public function testAnnounceOutputsFormattedEvent(): void
    {
        $io = $this->createMock(Io::class);
        $factory = $this->createStub(DatabaseManipulatorFactory::class);

        $io->expects($this->exactly(2))
            ->method('output')
            ->willReturnCallback(function ($text) {
                // Verify output has content
                $this->assertNotEmpty($text);
            });

        $migrations = new Migrations($io, $factory);
        $migrations->announce('add', 'table', ['table' => 'users', 'schema' => 'public', 'name' => 'users']);
    }
}
