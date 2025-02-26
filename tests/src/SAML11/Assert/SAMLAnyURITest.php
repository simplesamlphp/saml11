<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\Test\Assert;

use PHPUnit\Framework\Attributes\{CoversClass, DataProvider, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\Assert\AssertionFailedException;
use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\SAML11\Exception\ProtocolViolationException;
use SimpleSAML\XML\Exception\SchemaViolationException;

/**
 * Class \SimpleSAML\SAML11\Assert\SAMLAnyURITest
 *
 * @package simplesamlphp/saml11
 */
#[Group('assert')]
#[CoversClass(Assert::class)]
final class SAMLAnyURITest extends TestCase
{
    /**
     * @param boolean $shouldPass
     * @param string $uri
     */
    #[DataProvider('provideAnyURI')]
    public function testValidSAMLAnyURI(bool $shouldPass, string $uri): void
    {
        try {
            Assert::validSAMLAnyURI($uri);
            $this->assertTrue($shouldPass);
        } catch (AssertionFailedException | ProtocolViolationException | SchemaViolationException $e) {
            $this->assertFalse($shouldPass);
        } finally {
        }
    }


    /**
     * @return array<string, array{0: bool, 1: string}>
     */
    public static function provideAnyURI(): array
    {
        return [
            'valid' => [true, 'https://simplesamlphp.org'],
            'empty' => [false, ''],
            'whitespace' => [false, ' '],
        ];
    }
}
