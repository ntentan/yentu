<?php

namespace yentu\tests\cases\unit;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use yentu\database\DatabaseItem;
use yentu\database\EncapsulatedStack;

#[Group('unit')]
class EncapsulatedStackTest extends TestCase
{
    public function testPushPopTopAndHasItems(): void
    {
        $stack = new EncapsulatedStack();
        $this->assertFalse($stack->hasItems());

        $item1 = $this->createStub(DatabaseItem::class);
        $item2 = $this->createStub(DatabaseItem::class);

        $stack->push($item1);
        $this->assertTrue($stack->hasItems());
        $this->assertSame($item1, $stack->top());

        $stack->push($item2);
        $this->assertSame($item2, $stack->top());

        $popped2 = $stack->pop();
        $this->assertSame($item2, $popped2);
        $this->assertSame($item1, $stack->top());

        $popped1 = $stack->pop();
        $this->assertSame($item1, $popped1);
        $this->assertFalse($stack->hasItems());
    }

    public function testPurgeCallsCommitOnAllItems(): void
    {
        $stack = new EncapsulatedStack();

        $item1 = $this->createMock(DatabaseItem::class);
        $item1->expects($this->once())->method('commit');

        $item2 = $this->createMock(DatabaseItem::class);
        $item2->expects($this->once())->method('commit');

        $item3 = $this->createMock(DatabaseItem::class);
        $item3->expects($this->once())->method('commit');

        $stack->push($item1);
        $stack->push($item2);
        $stack->push($item3);

        $this->assertTrue($stack->hasItems());
        $stack->purge();
        $this->assertFalse($stack->hasItems());
    }
}
