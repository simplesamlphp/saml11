<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\Type;

use PHPUnit\Framework\Attributes\{CoversClass, DataProvider};
use PHPUnit\Framework\TestCase;
use SimpleSAML\Assert\AssertionFailedException;
use SimpleSAML\SAML11\Exception\ProtocolViolationException;
use SimpleSAML\SAML11\Type\DateTimeValue;
use SimpleSAML\XML\Exception\SchemaViolationException;

/**
 * Class \SimpleSAML\Test\SAML11\Type\DateTimeValueTest
 *
 * @package simplesamlphp/xml-common
 */
#[CoversClass(DateTimeValue::class)]
final class DateTimeValueTest extends TestCase
{
    /**
     * @param boolean $shouldPass
     * @param string $dateTime
     */
    #[DataProvider('provideDateTime')]
    public function testDateTime(bool $shouldPass, string $dateTime): void
    {
        try {
            DateTimeValue::fromString($dateTime);
            $this->assertTrue($shouldPass);
        } catch (AssertionFailedException | ProtocolViolationException | SchemaViolationException $e) {
            $this->assertFalse($shouldPass);
        }
    }


    /**
     * @return array<string, array{0: bool, 1: string}>
     */
    public static function provideDateTime(): array
    {
        return [
            'valid' => [true, '2001-10-26T21:32:52Z'],
            'invalid with numeric difference' => [false, '2001-10-26T21:32:52+02:00'],
            'invalid with Zulu' => [false, '2001-10-26T19:32:52'],
            'invalid with 00:00 difference' => [false, '2001-10-26T19:32:52+00:00'],
            'valid with negative value' => [true, '-2001-10-26T21:32:52Z'],
            'valid with subseconds' => [true, '2001-10-26T21:32:52.12679Z'],
            'valid with more than four digit year' => [true, '-22001-10-26T21:32:52Z'],
            'valid with sub-seconds' => [true, '2001-10-26T21:32:52.12679Z'],
            'empty' => [false, ''],
            'whitespace collapse' => [true, ' 2001-10-26T21:32:52Z '],
            'missing time' => [false, '2001-10-26'],
            'missing second' => [false, '2001-10-26T21:32'],
            'hour out of range' => [false, '2001-10-26T25:32:52+02:00'],
            'year 0000' => [false, '0000-10-26T25:32:52+02:00'],
            'prefixed zero' => [false, '02001-10-26T25:32:52+02:00'],
            'wrong format' => [false, '01-10-26T21:32'],
        ];
    }
}
