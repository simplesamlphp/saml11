<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Type\SAMLAnyURIValue;
use SimpleSAML\SAML11\XML\saml\{AbstractSamlElement, Audience};
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};

use function dirname;
use function strval;

/**
 * Tests for Audience elements.
 *
 * @package simplesamlphp/saml11
 */
#[Group('saml')]
#[CoversClass(Audience::class)]
#[CoversClass(AbstractSamlElement::class)]
final class AudienceTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = Audience::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Audience.xml',
        );
    }


    // marshalling


    /**
     * Test creating an Audience from scratch
     */
    public function testMarshalling(): void
    {
        $audience = new Audience(
            SAMLAnyURIValue::fromString('urn:x-simplesamlphp:audience'),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($audience),
        );
    }
}
