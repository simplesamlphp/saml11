<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\XML\saml\AbstractSamlElement;
use SimpleSAML\SAML11\XML\saml\AttributeValue;
use SimpleSAML\SAML11\XML\saml\NameIdentifier;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use TypeError;

use function dirname;
use function strval;

/**
 * Tests for AttributeValue elements.
 *
 * @package simplesamlphp/saml11
 */
#[CoversClass(AttributeValue::class)]
#[CoversClass(AbstractSamlElement::class)]
final class AttributeValueTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$schemaFile = dirname(__FILE__, 6) . '/resources/schemas/oasis-sstc-saml-schema-assertion-1.1.xsd';

        self::$testedClass = AttributeValue::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_AttributeValue.xml',
        );
    }


    // marshalling


    /**
     * Test creating an AttributeValue from scratch using an integer.
     */
    public function testMarshalling(): void
    {
        $av = new AttributeValue(2);
        $this->assertIsInt($av->getValue());
        $this->assertEquals(2, $av->getValue());
        $this->assertEquals('xs:integer', $av->getXsiType());

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($av),
        );
    }


    /**
     * Test creating an AttributeValue from scratch using a string.
     */
    public function testMarshallingString(): void
    {
        $av = new AttributeValue('value');

        $this->assertEquals('value', $av->getValue());
        $this->assertEquals('xs:string', $av->getXsiType());
    }


    /**
     * Test creating an AttributeValue from scratch using an integer.
     */
    public function testMarshallingInteger(): void
    {
        $av = new AttributeValue(3);

        $this->assertEquals(3, $av->getValue());
        $this->assertEquals('xs:integer', $av->getXsiType());

        $nssaml = C::NS_SAML;
        $nsxs = C::NS_XS;
        $nsxsi = C::NS_XSI;
        $xml = <<<XML
<saml:AttributeValue xmlns:saml="{$nssaml}" xmlns:xsi="{$nsxsi}" xmlns:xs="{$nsxs}" xsi:type="xs:integer">3</saml:AttributeValue>
XML;
        $this->assertEquals(
            $xml,
            strval($av),
        );
    }


    /**
     * Test creating an AttributeValue from scratch using an dateTime.
     */
    public function testMarshallingDateTime(): void
    {
        $av = new AttributeValue(new DateTimeImmutable("2024-04-04T04:44:44Z"));

        /** @var \DateTimeInterface $value */
        $value = $av->getValue();
        $this->assertEquals('2024-04-04T04:44:44Z', $value->format(C::DATETIME_FORMAT));
        $this->assertEquals('xs:dateTime', $av->getXsiType());

        $nssaml = C::NS_SAML;
        $nsxs = C::NS_XS;
        $nsxsi = C::NS_XSI;
        $xml = <<<XML
<saml:AttributeValue xmlns:saml="{$nssaml}" xmlns:xsi="{$nsxsi}" xmlns:xs="{$nsxs}" xsi:type="xs:dateTime">2024-04-04T04:44:44Z</saml:AttributeValue>
XML;
        $this->assertEquals(
            $xml,
            strval($av),
        );
    }


    /**
     * Verifies that supplying an empty string as attribute value will
     * generate a tag with no content (instead of e.g. an empty tag).
     *
     */
    public function testEmptyStringAttribute(): void
    {
        $av = new AttributeValue('');
        $xmlRepresentation = clone self::$xmlRepresentation;
        $xmlRepresentation->documentElement->textContent = '';
//        $this->assertEqualXMLStructure(
//            $this->xmlRepresentation->documentElement,
//            $av->toXML(),
//        );
        $this->assertEquals('', $av->getValue());
        $this->assertEquals('xs:string', $av->getXsiType());
    }


    // unmarshalling


    /**
     * Verifies that we can create an AttributeValue containing a NameID from a DOMElement.
     *
     * @return void
     */
    public function testUnmarshallingNameID(): void
    {
        $document = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_AttributeValueWithNameID.xml',
        );

        $av = AttributeValue::fromXML($document->documentElement);
        $value = $av->getValue();

        $this->assertInstanceOf(NameIdentifier::class, $value);

        $this->assertEquals('abcd-some-value-xyz', $value->getContent());
        $this->assertEquals('urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified', $value->getFormat());
        $this->assertXmlStringEqualsXmlString($document->saveXML(), $av->toXML()->ownerDocument?->saveXML());
    }


    /**
     * Verifies that we cannot create an AttributeValue that is nullable, like SAML2 allows, but SAML1.1 does not.
     *
     * @return void
     */
    public function testUnmarshallingNil(): void
    {
        $nssaml = C::NS_SAML;
        $nsxsi = C::NS_XSI;

        $xml = <<<XML
<saml:AttributeValue xmlns:saml="{$nssaml}" xmlns:xsi="{$nsxsi}" xsi:nil="1"/>
XML;

        $document = DOMDocumentFactory::fromString($xml);
        $this->expectException(TypeError::class);
        AttributeValue::fromXML($document->documentElement);
    }
}
