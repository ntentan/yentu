<?php

namespace yentu\tests\cases\unit;

use clearice\io\Io;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use yentu\commands\Status;
use yentu\factories\DatabaseManipulatorFactory;
use yentu\manipulators\AbstractDatabaseManipulator;
use yentu\Migrations;

#[Group('unit')]
class StatusCommandTest extends TestCase
{
    public function testStatusWithNoAppliedMigrationsOutputsMessage(): void
    {
        $manipulator = $this->createStub(AbstractDatabaseManipulator::class);
        $manipulator->method('getVersion')->willReturn(null);

        $factory = $this->createStub(DatabaseManipulatorFactory::class);
        $factory->method('createManipulator')->willReturn($manipulator);

        $migrations = $this->createStub(Migrations::class);

        $io = $this->createMock(Io::class);
        $io->expects($this->once())
            ->method('output')
            ->with("\nYou have not applied any migrations\n");

        $status = new Status($migrations, $factory, $io);
        $status->run();
    }

    public function testStatusWithAppliedAndPendingMigrations(): void
    {
        $manipulator = $this->createStub(AbstractDatabaseManipulator::class);
        $manipulator->method('getVersion')->willReturn('20260101000000');

        $factory = $this->createStub(DatabaseManipulatorFactory::class);
        $factory->method('createManipulator')->willReturn($manipulator);

        $migrations = $this->createStub(Migrations::class);
        $migrations->method('getRunMirations')->willReturn([
            '20260101000000' => [
                'timestamp' => '20260101000000',
                'migration' => 'initial_setup',
                'default_schema' => 'public',
            ]
        ]);
        $migrations->method('getAllMigrations')->willReturn([
            '20260101000000' => [
                'timestamp' => '20260101000000',
                'migration' => 'initial_setup',
            ],
            '20260102000000' => [
                'timestamp' => '20260102000000',
                'migration' => 'add_users',
            ],
        ]);

        $outputChunks = [];
        $io = $this->createMock(Io::class);
        $io->expects($this->atLeastOnce())
            ->method('output')
            ->willReturnCallback(function ($text) use (&$outputChunks) {
                $outputChunks[] = $text;
            });

        $status = new Status($migrations, $factory, $io);
        $status->run();

        $fullOutput = implode('', $outputChunks);
        $this->assertStringContainsString("1 migration(s) have been applied so far.", $fullOutput);
        $this->assertStringContainsString("20260101000000 initial_setup on `public` schema", $fullOutput);
        $this->assertStringContainsString("Last migration applied:\n    20260101000000 initial_setup", $fullOutput);
        $this->assertStringContainsString("1 migration(s) that could be applied.", $fullOutput);
        $this->assertStringContainsString("20260102000000 add_users", $fullOutput);
    }

    public function testStatusWithAllMigrationsAppliedNoPending(): void
    {
        $manipulator = $this->createStub(AbstractDatabaseManipulator::class);
        $manipulator->method('getVersion')->willReturn('20260101000000');

        $factory = $this->createStub(DatabaseManipulatorFactory::class);
        $factory->method('createManipulator')->willReturn($manipulator);

        $migrations = $this->createStub(Migrations::class);
        $migrations->method('getRunMirations')->willReturn([
            '20260101000000' => [
                'timestamp' => '20260101000000',
                'migration' => 'initial_setup',
                'default_schema' => '',
            ]
        ]);
        $migrations->method('getAllMigrations')->willReturn([
            '20260101000000' => [
                'timestamp' => '20260101000000',
                'migration' => 'initial_setup',
            ],
        ]);

        $outputChunks = [];
        $io = $this->createMock(Io::class);
        $io->expects($this->atLeastOnce())
            ->method('output')
            ->willReturnCallback(function ($text) use (&$outputChunks) {
                $outputChunks[] = $text;
            });

        $status = new Status($migrations, $factory, $io);
        $status->run();

        $fullOutput = implode('', $outputChunks);
        $this->assertStringContainsString("There are no pending migrations.", $fullOutput);
    }
}
