<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Type\{AnyURIValue, DateTimeValue};
use SimpleSAML\SAML11\XML\saml\{
    AbstractConditionsType,
    AbstractSamlElement,
    Audience,
    AudienceRestrictionCondition,
    Conditions,
    DoNotCacheCondition,
};
use SimpleSAML\Test\SAML11\CustomCondition;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};

use function dirname;
use function strval;

/**
 * Tests for Conditions elements.
 *
 * @package simplesamlphp/saml11
 */
#[Group('saml')]
#[CoversClass(Conditions::class)]
#[CoversClass(AbstractConditionsType::class)]
#[CoversClass(AbstractSamlElement::class)]
final class ConditionsTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$schemaFile = dirname(__FILE__, 5) . '/resources/schemas/simplesamlphp.xsd';

        self::$testedClass = Conditions::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Conditions.xml',
        );
    }


    // marshalling


    /**
     * Test creating an Conditions from scratch
     */
    public function testMarshalling(): void
    {
        $audience = new Audience(
            AnyURIValue::fromString('urn:x-simplesamlphp:audience'),
        );
        $audienceRestrictionCondition = new AudienceRestrictionCondition([$audience]);

        $doNotCacheCondition = new DoNotCacheCondition();

        $condition = new CustomCondition(
            [
                new Audience(
                    AnyURIValue::fromString('urn:some:audience'),
                ),
            ],
        );

        $conditions = new Conditions(
            [$audienceRestrictionCondition],
            [$doNotCacheCondition],
            [$condition],
            DateTimeValue::fromString('2023-01-24T09:42:26Z'),
            DateTimeValue::fromString('2023-01-24T09:47:26Z'),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($conditions),
        );
    }


    /**
     * Adding an empty Conditions element should yield an empty element.
     */
    public function testMarshallingEmptyElement(): void
    {
        $samlns = C::NS_SAML;
        $conditions = new Conditions([]);
        $this->assertEquals(
            "<saml:Conditions xmlns:saml=\"$samlns\"/>",
            strval($conditions),
        );
        $this->assertTrue($conditions->isEmptyElement());
    }
}
