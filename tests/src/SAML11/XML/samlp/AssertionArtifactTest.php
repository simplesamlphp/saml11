<?php

declare(strict_types=1);

namespace Simplesamlp\Test\SAML11\XML\samlp;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\XML\samlp\AbstractSamlpElement;
use SimpleSAML\SAML11\XML\samlp\AssertionArtifact;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;

use function dirname;
use function strval;

/**
 * Tests for AssertionArtifact elements.
 *
 * @package simplesamlpphp/saml11
 */
#[CoversClass(AssertionArtifact::class)]
#[CoversClass(AbstractSamlpElement::class)]
final class AssertionArtifactTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$schemaFile = dirname(__FILE__, 6) . '/resources/schemas/oasis-sstc-saml-schema-protocol-1.1.xsd';

        self::$testedClass = AssertionArtifact::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/samlp_AssertionArtifact.xml',
        );
    }


    // marshalling


    /**
     * Test creating an AssertionArtifact from scratch
     */
    public function testMarshalling(): void
    {
        $assertionArtifact = new AssertionArtifact('AAEbuqrPjR1XORIHk5YAV8I4sM0nKP2CLV+h1CMiWbnkaWvvlJ0g4Ess');

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($assertionArtifact),
        );
    }
}