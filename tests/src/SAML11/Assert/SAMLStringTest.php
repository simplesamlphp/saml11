<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\Test\Assert;

use PHPUnit\Framework\Attributes\{CoversClass, DataProvider, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\SAML11\Exception\ProtocolViolationException;
use SimpleSAML\XMLSchema\Exception\SchemaViolationException;

/**
 * Class \SimpleSAML\SAML11\Assert\SAMLStringTest
 *
 * @package simplesamlphp/saml11
 */
#[Group('assert')]
#[CoversClass(Assert::class)]
final class SAMLStringTest extends TestCase
{
    /**
     * @param boolean $shouldPass
     * @param string $str
     */
    #[DataProvider('provideString')]
    public function testValidSAMLString(bool $shouldPass, string $str): void
    {
        try {
            Assert::validSAMLString($str);
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
