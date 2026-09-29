<?php

namespace yentu\tests\cases\unit;

use clearice\io\Io;
use ntentan\atiaa\DefaultDriverFactory;
use ntentan\atiaa\Driver;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use yentu\exceptions\DatabaseManipulatorException;
use yentu\factories\DatabaseManipulatorFactory;
use yentu\manipulators\Sqlite;

#[Group('unit')]
class DatabaseManipulatorFactoryTest extends TestCase
{
    public function testCreateManipulatorSuccess(): void
    {
        $driverFactory = $this->createStub(DefaultDriverFactory::class);
        $driverFactory->method('getConfig')->willReturn([
            'driver' => 'sqlite',
            'file' => ':memory:',
        ]);

        $driver = $this->createStub(Driver::class);
        $driverFactory->method('createDriver')->willReturn($driver);

        $io = $this->createStub(Io::class);
        $factory = new DatabaseManipulatorFactory($driverFactory, $io);

        $manipulator = $factory->createManipulator();
        $this->assertInstanceOf(Sqlite::class, $manipulator);
    }

    public function testCreateManipulatorWithUnknownDriverThrowsException(): void
    {
        $driverFactory = $this->createStub(DefaultDriverFactory::class);
        $driverFactory->method('getConfig')->willReturn([
            'driver' => 'nonexistent_driver',
        ]);

        $io = $this->createStub(Io::class);
        $factory = new DatabaseManipulatorFactory($driverFactory, $io);

        $this->expectException(DatabaseManipulatorException::class);
        $this->expectExceptionMessage("Database manipulator class [\\yentu\\manipulators\\Nonexistent_driver] does not exist.");

        $factory->createManipulator();
    }

    public function testCreateManipulatorWithConfigSetsConfigOnDriverFactory(): void
    {
        $driverFactory = $this->createMock(DefaultDriverFactory::class);
        $driverFactory->expects($this->once())
            ->method('setConfig')
            ->with($this->callback(function ($config) {
                return isset($config['driver']) && $config['driver'] === 'sqlite';
            }));

        $driverFactory->method('getConfig')->willReturn([
            'driver' => 'sqlite',
            'file' => ':memory:',
        ]);

        $driver = $this->createStub(Driver::class);
        $driverFactory->method('createDriver')->willReturn($driver);

        $io = $this->createStub(Io::class);
        $factory = new DatabaseManipulatorFactory($driverFactory, $io);

        $manipulator = $factory->createManipulatorWithConfig(['driver' => 'sqlite', 'file' => ':memory:']);
        $this->assertInstanceOf(Sqlite::class, $manipulator);
    }
}
