<?php

namespace yentu\tests\cases\unit;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use yentu\ChangeLogger;
use yentu\database\EncapsulatedStack;
use yentu\database\ItemType;
use yentu\database\Schema;
use yentu\factories\DatabaseItemFactory;

#[Group('unit')]
class DatabaseItemFactoryTest extends TestCase
{
    public function testGetDefaultSchemaReturnsSchemaInstance(): void
    {
        $stack = new EncapsulatedStack();
        $factory = new DatabaseItemFactory($stack, ['home' => '/custom/path']);

        $schema = $factory->getDefaultSchema('public');
        $this->assertInstanceOf(Schema::class, $schema);
        $this->assertEquals('public', $schema->getName());
    }

    public function testGetEncapsulatedStackReturnsSameStackInstance(): void
    {
        $stack = new EncapsulatedStack();
        $factory = new DatabaseItemFactory($stack, ['home' => '/custom/path']);

        $this->assertSame($stack, $factory->getEncapsulatedStack());
    }

    public function testCreateInstantiatesItemAndPushesToStack(): void
    {
        $stack = new EncapsulatedStack();
        $changeLogger = $this->createStub(ChangeLogger::class);

        $factory = new DatabaseItemFactory($stack, ['home' => '/custom/path']);
        $factory->setChangeLogger($changeLogger);

        $this->assertFalse($stack->hasItems());

        $item = $factory->create(ItemType::Schema, 'test_schema');

        $this->assertInstanceOf(Schema::class, $item);
        $this->assertTrue($stack->hasItems());
        $this->assertSame($item, $stack->top());
    }
}
