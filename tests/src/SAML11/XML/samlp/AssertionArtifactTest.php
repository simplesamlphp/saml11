<?php

declare(strict_types=1);

namespace Simplesamlp\Test\SAML11\XML\samlp;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Type\SAMLStringValue;
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
#[Group('samlp')]
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
        $assertionArtifact = new AssertionArtifact(
            SAMLStringValue::fromString('AAEbuqrPjR1XORIHk5YAV8I4sM0nKP2CLV+h1CMiWbnkaWvvlJ0g4Ess'),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($assertionArtifact),
        );
    }
}
