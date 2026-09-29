<?php

namespace yentu\tests\cases\unit;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use yentu\ChangeReverser;
use yentu\manipulators\AbstractDatabaseManipulator;

#[Group('unit')]
class ChangeReverserTest extends TestCase
{
    public function testReverseAddTableCallsDropTableOnDriver(): void
    {
        $driver = $this->createMock(AbstractDatabaseManipulator::class);
        $driver->expects($this->once())
            ->method('__call')
            ->with('dropTable', [['name' => 'users']])
            ->willReturn(true);

        $reverser = new ChangeReverser();
        $reverser->setDriver($driver);

        $result = $reverser->call('addTable', [['name' => 'users']]);
        $this->assertTrue($result);
    }

    public function testReverseDropTableCallsAddTableOnDriver(): void
    {
        $driver = $this->createMock(AbstractDatabaseManipulator::class);
        $driver->expects($this->once())
            ->method('__call')
            ->with('addTable', [['name' => 'posts']])
            ->willReturn(true);

        $reverser = new ChangeReverser();
        $reverser->setDriver($driver);

        $result = $reverser->call('dropTable', [['name' => 'posts']]);
        $this->assertTrue($result);
    }

    public function testReverseChangeColumnNameSwapsFromAndTo(): void
    {
        $driver = $this->createMock(AbstractDatabaseManipulator::class);
        $driver->expects($this->once())
            ->method('__call')
            ->with('changeColumnName', [[
                'to' => ['name' => 'old_name'],
                'from' => ['name' => 'new_name'],
            ]])
            ->willReturn(true);

        $reverser = new ChangeReverser();
        $reverser->setDriver($driver);

        $args = [
            [
                'from' => ['name' => 'old_name'],
                'to' => ['name' => 'new_name'],
            ]
        ];

        $result = $reverser->call('changeColumnName', $args);
        $this->assertTrue($result);
    }

    public function testReverseExecuteQueryCallsReverseQuery(): void
    {
        $driver = $this->createMock(AbstractDatabaseManipulator::class);
        $driver->expects($this->once())
            ->method('__call')
            ->with('reverseQuery', [['query' => 'CREATE TABLE test']])
            ->willReturn(true);

        $reverser = new ChangeReverser();
        $reverser->setDriver($driver);

        $result = $reverser->call('executeQuery', [['query' => 'CREATE TABLE test']]);
        $this->assertTrue($result);
    }
}
