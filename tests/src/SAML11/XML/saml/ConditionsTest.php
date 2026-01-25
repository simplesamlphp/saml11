<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Type\SAMLAnyURIValue;
use SimpleSAML\SAML11\Type\SAMLDateTimeValue;
use SimpleSAML\SAML11\XML\saml\AbstractConditionsType;
use SimpleSAML\SAML11\XML\saml\AbstractSamlElement;
use SimpleSAML\SAML11\XML\saml\Audience;
use SimpleSAML\SAML11\XML\saml\AudienceRestrictionCondition;
use SimpleSAML\SAML11\XML\saml\Conditions;
use SimpleSAML\SAML11\XML\saml\DoNotCacheCondition;
use SimpleSAML\Test\SAML11\CustomCondition;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;

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
            SAMLAnyURIValue::fromString('urn:x-simplesamlphp:audience'),
        );
        $audienceRestrictionCondition = new AudienceRestrictionCondition([$audience]);

        $doNotCacheCondition = new DoNotCacheCondition();

        $condition = new CustomCondition(
            [
                Audience::fromString('urn:some:audience'),
            ],
        );

        $conditions = new Conditions(
            [$audienceRestrictionCondition],
            [$doNotCacheCondition],
            [$condition],
            SAMLDateTimeValue::fromString('2023-01-24T09:42:26Z'),
            SAMLDateTimeValue::fromString('2023-01-24T09:47:26Z'),
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
