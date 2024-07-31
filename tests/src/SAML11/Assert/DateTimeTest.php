<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\Test\Assert;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Assert\Assert as SAML11Assert;
use SimpleSAML\SAML11\Exception\ProtocolViolationException;
use SimpleSAML\XML\Exception\SchemaViolationException;

/**
 * Class \SimpleSAML\SAML11\Assert\DateTimeTest
 *
 * @package simplesamlphp/saml11
 */
#[CoversClass(SAML11Assert::class)]
final class DateTimeTest extends TestCase
{
    /**
     * @param boolean $shouldPass
     * @param string $timestamp
     */
    #[DataProvider('provideDateTime')]
    public function testValidDateTime(bool $shouldPass, string $timestamp): void
    {
        try {
            SAML11Assert::validDateTime($timestamp);
            $this->assertTrue($shouldPass);
        } catch (ProtocolViolationException | SchemaViolationException $e) {
            $this->assertFalse($shouldPass);
        }
    }


    /**
     * @return array<string, array{0: bool, 1: string}>
     */
    public static function provideDateTime(): array
    {
        return [
            'sub-second zulu' => [true, '2016-07-27T19:30:00.123Z'],
            'zulu' => [true, '2016-07-27T19:30:00Z'],
            'sub-second offset' => [false, '2016-07-27T19:30:00.123+05:00'],
            'offset' => [false, '2016-07-27T19:30:00+05:00'],
            'bogus' => [false, '&*$(#&^@!(^%$'],
            'whitespace' => [false, ' '],
        ];
    }
}
