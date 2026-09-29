<?php

namespace yentu\tests\cases\unit;

use clearice\io\Io;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use yentu\commands\Create;
use yentu\exceptions\CommandException;
use yentu\Migrations;

#[Group('unit')]
class CreateCommandTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/yentu_unit_test_' . uniqid();
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

    private function createMigrationsMock(): Migrations
    {
        $migrations = $this->createStub(Migrations::class);
        $migrations->method('getPath')
            ->willReturnCallback(function ($path) {
                return $this->tempDir . '/' . $path;
            });
        return $migrations;
    }

    public function testRunWithoutNameThrowsCommandException(): void
    {
        $migrations = $this->createStub(Migrations::class);
        $io = $this->createStub(Io::class);
        $command = new Create($migrations, $io);

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("Please provide a name for your new migration");

        $command->run();
    }

    public function testRunWithEmptyNameThrowsCommandException(): void
    {
        $migrations = $this->createMigrationsMock();
        $io = $this->createStub(Io::class);
        $command = new Create($migrations, $io);
        $command->setOptions(['__args' => ['']]);

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("Please provide a name for your new migration");

        $command->run();
    }

    public function testRunWithInvalidCharactersThrowsCommandException(): void
    {
        $migrations = $this->createMigrationsMock();
        $io = $this->createStub(Io::class);
        $command = new Create($migrations, $io);
        $command->setOptions(['__args' => ['123_invalid_start']]);

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("Migration names must always start with a lowercase alphabet");

        $command->run();
    }

    public function testRunWithExistingMigrationThrowsCommandException(): void
    {
        file_put_contents("{$this->tempDir}/migrations/20260101000000_add_users.php", "<?php");

        $migrations = $this->createMigrationsMock();
        $io = $this->createStub(Io::class);
        $command = new Create($migrations, $io);
        $command->setOptions(['__args' => ['add_users']]);

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("A migration already exists with the name add_users");

        $command->run();
    }

    public function testRunWithNonExistentMigrationsDirectoryThrowsCommandException(): void
    {
        $migrations = $this->createStub(Migrations::class);
        $migrations->method('getPath')->willReturn($this->tempDir . '/nonexistent_migrations/');

        $io = $this->createStub(Io::class);
        $command = new Create($migrations, $io);
        $command->setOptions(['__args' => ['add_users']]);

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("The migrations directory");

        $command->run();
    }

    public function testSuccessfulCreateWritesFileAndOutputsMessage(): void
    {
        $migrations = $this->createMigrationsMock();
        $io = $this->createMock(Io::class);
        $io->expects($this->once())
            ->method('output')
            ->with($this->stringContains("Added {$this->tempDir}/migrations/"));

        $command = new Create($migrations, $io);
        $command->setOptions(['__args' => ['create_posts']]);

        $command->run();

        $files = glob("{$this->tempDir}/migrations/*_create_posts.php");
        $this->assertCount(1, $files);
        $contents = file_get_contents($files[0]);
        $this->assertStringContainsString('\yentu\Yentu::begin()', $contents);
        $this->assertStringContainsString('->end();', $contents);
    }
}
