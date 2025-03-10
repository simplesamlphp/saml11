<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\XML\saml\AbstractAttributeDesignatorType;
use SimpleSAML\SAML11\XML\saml\AbstractAttributeType;
use SimpleSAML\SAML11\XML\saml\AbstractSamlElement;
use SimpleSAML\SAML11\XML\saml\Attribute;
use SimpleSAML\SAML11\XML\saml\AttributeValue;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\SAML11\XML\saml\AttributeTest
 *
 * @package simplesamlphp/saml11
 */
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
        self::$schemaFile = dirname(__FILE__, 6) . '/resources/schemas/oasis-sstc-saml-schema-assertion-1.1.xsd';

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
            'TheName',
            'https://example.org/',
            [
                new AttributeValue('FirstValue'),
                new AttributeValue('SecondValue'),
                new AttributeValue(3),
                new AttributeValue(new DateTimeImmutable('2024-04-04T04:44:44Z')),
            ],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($attribute),
        );
    }
}
