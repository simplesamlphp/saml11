<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Type\{SAMLAnyURIValue, SAMLDateTimeValue, SAMLStringValue};
use SimpleSAML\SAML11\XML\saml\{
    AbstractAttributeDesignatorType,
    AbstractAttributeType,
    AbstractSamlElement,
    Attribute,
    AttributeValue,
};
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XML\Type\IntegerValue;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\SAML11\XML\saml\AttributeTest
 *
 * @package simplesamlphp/saml11
 */
#[Group('saml')]
#[CoversClass(Attribute::class)]
#[CoversClass(AttributeValue::class)]
#[CoversClass(AbstractAttributeType::class)]
#[CoversClass(AbstractAttributeDesignatorType::class)]
#[CoversClass(AbstractSamlElement::class)]
final class AttributeTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = Attribute::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Attribute.xml',
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $attribute = new Attribute(
            SAMLStringValue::fromString('TheName'),
            SAMLAnyURIValue::fromString('https://example.org/'),
            [
                new AttributeValue(
                    SAMLStringValue::fromString('FirstValue'),
                ),
                new AttributeValue(
                    SAMLStringValue::fromString('SecondValue'),
                ),
                new AttributeValue(
                    IntegerValue::fromString('3'),
                ),
                new AttributeValue(
                    SAMLDateTimeValue::fromString('2024-04-04T04:44:44Z'),
                ),
            ],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($attribute),
        );
    }
}
