<?php

namespace yentu\tests\cases\unit;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use yentu\Uuid;

#[Group('unit')]
class UuidTest extends TestCase
{
    public function testGenerateUuidV7DefaultString(): void
    {
        $uuid = Uuid::v7(false);
        $this->assertEquals(36, strlen($uuid));
        $this->assertTrue(Uuid::isValid($uuid));
        // Check UUID version 7 at position 14 (index 14)
        $this->assertEquals('7', $uuid[14]);
        // Check UUID variant (8, 9, a, or b) at position 19 (index 19)
        $this->assertContains($uuid[19], ['8', '9', 'a', 'b']);
    }

    public function testGenerateUuidV7Binary(): void
    {
        $bin = Uuid::v7(true);
        $this->assertEquals(16, strlen($bin));
        $hex = bin2hex($bin);
        $this->assertEquals(32, strlen($hex));
        $this->assertEquals('7', $hex[12]);
        $this->assertContains($hex[16], ['8', '9', 'a', 'b']);
    }

    public function testToBinaryAndToStringRoundTrip(): void
    {
        $uuid = Uuid::v7(false);
        $bin = Uuid::toBinary($uuid);
        $this->assertEquals(16, strlen($bin));

        $restored = Uuid::toString($bin);
        $this->assertEquals(strtolower($uuid), strtolower($restored));
    }

    public function testToBinaryWithAlreadyBinaryInput(): void
    {
        $bin = random_bytes(16);
        $this->assertSame($bin, Uuid::toBinary($bin));
    }

    public function testToBinaryWithInvalidUuidThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Uuid::toBinary('not-a-uuid');
    }

    public function testToStringWithInvalidInputThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Uuid::toString('invalid');
    }

    public function testIsValid(): void
    {
        $this->assertTrue(Uuid::isValid('019310a0-0000-7000-8000-000000000000'));
        $this->assertTrue(Uuid::isValid('019310a0000070008000000000000000'));
        $this->assertFalse(Uuid::isValid('019310a0-0000-7000-8000'));
        $this->assertFalse(Uuid::isValid('not-a-valid-uuid-string-at-all!!'));
    }

    public function testGlobalUuid7Function(): void
    {
        $uuid = uuid7(false);
        $this->assertTrue(Uuid::isValid($uuid));
        $this->assertEquals('7', $uuid[14]);

        $bin = uuid7(true);
        $this->assertEquals(16, strlen($bin));
    }
}
