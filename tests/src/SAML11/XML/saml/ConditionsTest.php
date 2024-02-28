<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\XML\saml\Audience;
use SimpleSAML\SAML11\XML\saml\AudienceRestrictionCondition;
use SimpleSAML\SAML11\XML\saml\Condition;
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
 * @covers \SimpleSAML\SAML11\XML\saml\Conditions
 * @covers \SimpleSAML\SAML11\XML\saml\AbstractConditionsType
 * @covers \SimpleSAML\SAML11\XML\saml\AbstractSamlElement
 *
 * @package simplesamlphp/saml11
 */
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
        $audience = new Audience('urn:x-simplesamlphp:audience');
        $audienceRestrictionCondition = new AudienceRestrictionCondition([$audience]);

        $doNotCacheCondition = new DoNotCacheCondition();

        $condition = new CustomCondition(
            [new Audience('urn:some:audience')],
        );

        $conditions = new Conditions(
            [$audienceRestrictionCondition],
            [$doNotCacheCondition],
            [$condition],
            new DateTimeImmutable('2023-01-24T09:42:26Z'),
            new DateTimeImmutable('2023-01-24T09:47:26Z'),
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
