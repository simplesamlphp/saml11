<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\Type;

use PHPUnit\Framework\Attributes\{CoversClass, DataProvider, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Exception\ProtocolViolationException;
use SimpleSAML\SAML11\Type\SAMLStringValue;
use SimpleSAML\XMLSchema\Exception\SchemaViolationException;

/**
 * Class \SimpleSAML\Test\SAML11\Type\SAMLStringValueValueTest
 *
 * @package simplesamlphp/saml11
 */
#[Group('type')]
#[CoversClass(SAMLStringValue::class)]
final class SAMLStringValueTest extends TestCase
{
    /**
     * @param boolean $shouldPass
     * @param string $stringValue
     */
    #[DataProvider('provideString')]
    public function testSAMLString(bool $shouldPass, string $stringValue): void
    {
        try {
            SAMLStringValue::fromString($stringValue);
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
