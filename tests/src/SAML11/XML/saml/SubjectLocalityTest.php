<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Type\StringValue;
use SimpleSAML\SAML11\XML\saml\{AbstractSamlElement, AbstractSubjectLocalityType, SubjectLocality};
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};

use function dirname;
use function strval;

/**
 * Tests for SubjectLocality elements.
 *
 * @package simplesamlphp/saml11
 */
#[Group('saml')]
#[CoversClass(SubjectLocality::class)]
#[CoversClass(AbstractSubjectLocalityType::class)]
#[CoversClass(AbstractSamlElement::class)]
final class SubjectLocalityTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = SubjectLocality::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_SubjectLocality.xml',
        );
    }


    // marshalling


    /**
     * Test creating an SubjectLocality from scratch
     */
    public function testMarshalling(): void
    {
        $sl = new SubjectLocality(
            StringValue::fromString('127.0.0.1'),
            StringValue::fromString('simplesamlphp.org'),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($sl),
        );
    }


    /**
     * Test creating an empty SubjectLocality from scratch
     */
    public function testMarshallingEmpty(): void
    {
        $sl = new SubjectLocality();
        $this->assertTrue($sl->isEmptyElement());
    }
}
