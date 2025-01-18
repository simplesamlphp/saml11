<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\Test\Assert;

use PHPUnit\Framework\Attributes\{CoversClass, DataProvider};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\SAML11\Exception\ProtocolViolationException;
use SimpleSAML\XML\Exception\SchemaViolationException;

/**
 * Class \SimpleSAML\SAML11\Assert\StringTest
 *
 * @package simplesamlphp/saml11
 */
#[CoversClass(Assert::class)]
final class StringTest extends TestCase
{
    /**
     * @param boolean $shouldPass
     * @param string $str
     */
    #[DataProvider('provideString')]
    public function testValidString(bool $shouldPass, string $str): void
    {
        try {
            Assert::validString($str);
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
            'valid' => [true, 'dear diary'],
            'empty' => [false, ''],
            'whitespace' => [false, ' '],
        ];
    }
}
