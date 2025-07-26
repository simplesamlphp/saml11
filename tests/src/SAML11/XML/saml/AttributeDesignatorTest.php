<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\XML\saml\{AbstractAttributeDesignatorType, AbstractSamlElement, AttributeDesignator};
use SimpleSAML\SAML11\Type\{SAMLAnyURIValue, SAMLStringValue};
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\SAML11\XML\saml\AttributeDesignatorTest
 *
 * @package simplesamlphp/saml11
 */
#[Group('saml')]
#[CoversClass(AttributeDesignator::class)]
#[CoversClass(AbstractAttributeDesignatorType::class)]
#[CoversClass(AbstractSamlElement::class)]
final class AttributeDesignatorTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = AttributeDesignator::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_AttributeDesignator.xml',
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $attributeDesignator = new AttributeDesignator(
            SAMLStringValue::fromString('TheName'),
            SAMLAnyURIValue::fromString('https://example.org/'),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($attributeDesignator),
        );
    }
}
