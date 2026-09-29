<?php

namespace yentu\tests\cases\unit;

use clearice\argparser\ArgumentParser;
use clearice\io\Io;
use ntentan\atiaa\exceptions\DatabaseDriverException;
use ntentan\utils\exceptions\FileNotFoundException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use yentu\Cli;
use yentu\commands\Command;
use yentu\exceptions\CommandException;
use yentu\exceptions\NonReversibleCommandException;

#[Group('unit')]
class CliTest extends TestCase
{
    private function createIoMock(): Io
    {
        return $this->createMock(Io::class);
    }

    private function createArgumentParserStub(): ArgumentParser
    {
        return $this->createStub(ArgumentParser::class);
    }

    public function testRunWithNullCommandDisplaysHelpAndReturnsZero(): void
    {
        $io = $this->createIoMock();
        $parser = $this->createArgumentParserStub();
        $parser->method('getHelpMessage')->willReturn("Usage instructions");
        $io->expects($this->once())->method('error')->with("Usage instructions");

        $cli = new Cli($io, $parser, []);
        $status = $cli->run();

        $this->assertEquals(0, $status);
    }

    public function testRunCommandSuccessReturnsZero(): void
    {
        $io = $this->createStub(Io::class);
        $parser = $this->createArgumentParserStub();
        $command = $this->createMock(Command::class);
        $command->expects($this->once())->method('run');

        $cli = new Cli($io, $parser, [], $command);
        $status = $cli->run();

        $this->assertEquals(0, $status);
    }

    public function testRunCommandThrowsNonReversibleCommandExceptionReturnsOne(): void
    {
        $io = $this->createIoMock();
        $parser = $this->createArgumentParserStub();
        $command = $this->createStub(Command::class);
        $command->method('run')->willThrowException(new NonReversibleCommandException("Fatal error"));

        $io->expects($this->once())->method('error')->with("Error: Fatal error\n");

        $cli = new Cli($io, $parser, [], $command);
        $status = $cli->run();

        $this->assertEquals(1, $status);
    }

    public function testRunCommandThrowsDatabaseDriverExceptionReversesAndReturnsTwo(): void
    {
        $io = $this->createIoMock();
        $parser = $this->createArgumentParserStub();
        $command = $this->createMock(Command::class);
        $command->method('run')->willThrowException(new DatabaseDriverException("Connection dropped"));
        $command->expects($this->once())->method('reverse');

        $io->expects($this->once())->method('error')->with("Database error: Connection dropped\n");

        $cli = new Cli($io, $parser, [], $command);
        $status = $cli->run();

        $this->assertEquals(2, $status);
    }

    public function testRunCommandThrowsYentuExceptionReversesAndReturnsThree(): void
    {
        $io = $this->createIoMock();
        $parser = $this->createArgumentParserStub();
        $command = $this->createMock(Command::class);
        $command->method('run')->willThrowException(new CommandException("Reversible failure"));
        $command->expects($this->once())->method('reverse');

        $io->expects($this->once())->method('error')->with("Error: Reversible failure\n");

        $cli = new Cli($io, $parser, [], $command);
        $status = $cli->run();

        $this->assertEquals(3, $status);
    }

    public function testRunCommandThrowsPdoExceptionReturnsFour(): void
    {
        $io = $this->createIoMock();
        $parser = $this->createArgumentParserStub();
        $command = $this->createStub(Command::class);
        $command->method('run')->willThrowException(new \PDOException("Cannot connect"));

        $io->expects($this->once())->method('error')->with("Failed to connect to database: Cannot connect\n");

        $cli = new Cli($io, $parser, [], $command);
        $status = $cli->run();

        $this->assertEquals(4, $status);
    }

    public function testRunCommandThrowsFileNotFoundExceptionReturnsFive(): void
    {
        $io = $this->createIoMock();
        $parser = $this->createArgumentParserStub();
        $command = $this->createStub(Command::class);
        $command->method('run')->willThrowException(new FileNotFoundException("Config missing"));

        $io->expects($this->once())->method('error')->with("Config missing\n");

        $cli = new Cli($io, $parser, [], $command);
        $status = $cli->run();

        $this->assertEquals(5, $status);
    }

    public function testDebugPrintsExceptionStackTrace(): void
    {
        $io = $this->createIoMock();
        $parser = $this->createArgumentParserStub();
        $command = $this->createStub(Command::class);
        $exception = new NonReversibleCommandException("Debugged error");
        $command->method('run')->willThrowException($exception);

        // Expects the message error and the stack trace error
        $io->expects($this->exactly(2))->method('error');

        $cli = new Cli($io, $parser, ['debug' => true], $command);
        $status = $cli->run();

        $this->assertEquals(1, $status);
    }
}
