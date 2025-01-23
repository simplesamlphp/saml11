<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\Type;

use PHPUnit\Framework\Attributes\{CoversClass, DataProvider};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Exception\ProtocolViolationException;
use SimpleSAML\SAML11\Type\StringValue;
use SimpleSAML\XML\Exception\SchemaViolationException;

/**
 * Class \SimpleSAML\Test\SAML11\Type\StringValueValueTest
 *
 * @package simplesamlphp/xml-common
 */
#[CoversClass(StringValue::class)]
final class StringValueTest extends TestCase
{
    /**
     * @param boolean $shouldPass
     * @param string $stringValue
     */
    #[DataProvider('provideString')]
    public function testString(bool $shouldPass, string $stringValue): void
    {
        try {
            StringValue::fromString($stringValue);
            $this->assertTrue($shouldPass);
        } catch (ProtocolViolationException | SchemaViolationException $e) {
            $this->assertFalse($shouldPass);
        }
    }


    /**
     * @return array<string, array{0: bool, 1: string}>
     */
    public static function provideString(): array
    {
        return [
            'empty string' => [false, ''],
            'some thing' => [true, 'Snoopy  '],
        ];
    }
}
