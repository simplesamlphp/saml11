<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Type\SAMLStringValue;
use SimpleSAML\SAML11\XML\saml\{AbstractSamlElement, NameIdentifier, SubjectConfirmationData};
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XML\Type\IntegerValue;

use function dirname;
use function strval;

/**
 * Tests for SubjectConfirmationData elements.
 *
 * @package simplesamlphp/saml11
 */
#[Group('saml')]
#[CoversClass(SubjectConfirmationData::class)]
#[CoversClass(AbstractSamlElement::class)]
final class SubjectConfirmationDataTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = SubjectConfirmationData::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_SubjectConfirmationData.xml',
        );
    }


    // marshalling


    /**
     * Test creating an SubjectConfirmationData from scratch using an integer.
     */
    public function testMarshalling(): void
    {
        $scd = new SubjectConfirmationData(
            IntegerValue::fromString('2'),
        );
        $this->assertInstanceOf(IntegerValue::class, $scd->getValue());
        $this->assertEquals('2', strval($scd->getValue()));
        $this->assertEquals('xs:integer', $scd->getXsiType());

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($scd),
        );
    }


    /**
     * Test creating an SubjectConfirmationData from scratch using a string.
     */
    public function testMarshallingString(): void
    {
        $scd = new SubjectConfirmationData(
            SAMLStringValue::fromString('value'),
        );

        $this->assertInstanceOf(SAMLStringValue::class, $scd->getValue());
        $this->assertEquals('value', strval($scd->getValue()));
        $this->assertEquals('xs:string', $scd->getXsiType());
    }


    /**
     */
    public function testMarshallingNull(): void
    {
        $scd = new SubjectConfirmationData(null);
        $this->assertNull($scd->getValue());
        $this->assertEquals('xs:nil', $scd->getXsiType());
        $nssaml = C::NS_SAML;
        $nsxsi = C::NS_XSI;
        $xml = <<<XML
<saml:SubjectConfirmationData xmlns:saml="{$nssaml}" xmlns:xsi="{$nsxsi}" xsi:nil="1"/>
XML;
        $this->assertEquals(
            $xml,
            strval($scd),
        );
    }


    // unmarshalling


    /**
     * Verifies that we can create an SubjectConfirmationData containing a NameID from a DOMElement.
     *
     * @return void
     */
    public function testUnmarshallingNameID(): void
    {
        $document = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_SubjectConfirmationDataWithNameID.xml',
        );

        $scd = SubjectConfirmationData::fromXML($document->documentElement);
        $value = $scd->getValue();

        $this->assertInstanceOf(NameIdentifier::class, $value);

        $this->assertEquals('abcd-some-value-xyz', $value->getValue());
        $this->assertEquals('urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified', $value->getFormat());
        $this->assertXmlStringEqualsXmlString($document->saveXML(), $scd->toXML()->ownerDocument?->saveXML());
    }
}
