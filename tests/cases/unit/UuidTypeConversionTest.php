<?php

namespace yentu\tests\cases\unit;

use clearice\io\Io;
use ntentan\atiaa\Driver;
use ntentan\atiaa\DriverFactoryInterface;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use yentu\manipulators\AbstractDatabaseManipulator;
use yentu\manipulators\Mysql;
use yentu\manipulators\Postgresql;
use yentu\manipulators\Sqlite;
use yentu\Uuid;

#[Group('unit')]
class UuidTypeConversionTest extends TestCase
{
    private function createManipulator(string $class): AbstractDatabaseManipulator
    {
        $driver = $this->createStub(Driver::class);
        $driver->method('getDefaultSchema')->willReturn('public');
        $driver->method('quoteIdentifier')->willReturnCallback(fn($x) => "`$x`");

        $driverFactory = $this->createStub(DriverFactoryInterface::class);
        $driverFactory->method('createDriver')->willReturn($driver);

        $io = $this->createStub(Io::class);

        return new $class($driverFactory, $io);
    }

    public function testPostgresqlUuidConversion(): void
    {
        $pg = $this->createManipulator(Postgresql::class);

        // To driver
        $this->assertEquals('uuid', $pg->convertTypes('uuid', AbstractDatabaseManipulator::CONVERT_TO_DRIVER));

        // To yentu
        $this->assertEquals('uuid', $pg->convertTypes('uuid', AbstractDatabaseManipulator::CONVERT_TO_YENTU));

        // Format UUID
        $uuid = '019310a0-0000-7000-8000-000000000000';
        $this->assertEquals($uuid, $pg->formatUuid($uuid));
    }

    public function testMysqlUuidConversion(): void
    {
        $mysql = $this->createManipulator(Mysql::class);

        // To driver: converts 'uuid' to 'binary(16)'
        $this->assertEquals('binary(16)', $mysql->convertTypes('uuid', AbstractDatabaseManipulator::CONVERT_TO_DRIVER));
        // Even if length is passed as 255 (the default in Mysql::addColumn)
        $this->assertEquals('binary(16)', $mysql->convertTypes('uuid', AbstractDatabaseManipulator::CONVERT_TO_DRIVER, 255));

        // To yentu: converts 'binary' with length 16 to 'uuid'
        $this->assertEquals('uuid', $mysql->convertTypes('binary', AbstractDatabaseManipulator::CONVERT_TO_YENTU, 16));
        $this->assertEquals('uuid', $mysql->convertTypes('binary(16)', AbstractDatabaseManipulator::CONVERT_TO_YENTU));

        // Format UUID converts string to 16 binary bytes
        $uuid = '019310a0-0000-7000-8000-000000000000';
        $bin = $mysql->formatUuid($uuid);
        $this->assertEquals(16, strlen($bin));
        $this->assertEquals($uuid, Uuid::toString($bin));
    }

    public function testSqliteUuidConversion(): void
    {
        $sqlite = $this->createManipulator(Sqlite::class);

        // To driver: converts 'uuid' to 'blob'
        $this->assertEquals('blob', $sqlite->convertTypes('uuid', AbstractDatabaseManipulator::CONVERT_TO_DRIVER, null));

        // To yentu: declared 'uuid' converts to 'uuid'
        $this->assertEquals('uuid', $sqlite->convertTypes('uuid', AbstractDatabaseManipulator::CONVERT_TO_YENTU, null));

        // Generic 'blob' remains 'blob'
        $this->assertEquals('blob', $sqlite->convertTypes('blob', AbstractDatabaseManipulator::CONVERT_TO_YENTU, null));

        // Format UUID converts string to 16 binary bytes
        $uuid = '019310a0-0000-7000-8000-000000000000';
        $bin = $sqlite->formatUuid($uuid);
        $this->assertEquals(16, strlen($bin));
        $this->assertEquals($uuid, Uuid::toString($bin));
    }
}
