<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\XML\saml\Audience;
use SimpleSAML\SAML11\XML\saml\AudienceRestrictionCondition;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;

use function dirname;
use function strval;

/**
 * Tests for AudienceRestictionCondition elements.
 *
 * @covers \SimpleSAML\SAML11\XML\saml\AudienceRestrictionCondition
 * @covers \SimpleSAML\SAML11\XML\saml\AbstractConditionType
 * @covers \SimpleSAML\SAML11\XML\saml\AbstractSamlElement
 *
 * @package simplesamlphp/saml11
 */
final class AudienceRestrictionConditionTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$schemaFile = dirname(__FILE__, 6) . '/resources/schemas/oasis-sstc-saml-schema-assertion-1.1.xsd';

        self::$testedClass = AudienceRestrictionCondition::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_AudienceRestrictionCondition.xml',
        );
    }


    // marshalling


    /**
     * Test creating an AudienceRestriction from scratch
     */
    public function testMarshalling(): void
    {
        $audience = new Audience('urn:x-simplesamlphp:audience');
        $audienceRestrictionCondition = new AudienceRestrictionCondition([$audience]);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($audienceRestrictionCondition),
        );
    }
}
