<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Type\SAMLAnyURIValue;
use SimpleSAML\SAML11\XML\saml\AbstractAudienceRestrictionConditionType;
use SimpleSAML\SAML11\XML\saml\AbstractSamlElement;
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
 * @package simplesamlphp/saml11
 */
#[Group('saml')]
#[CoversClass(AudienceRestrictionCondition::class)]
#[CoversClass(AbstractAudienceRestrictionConditionType::class)]
#[CoversClass(AbstractSamlElement::class)]
final class AudienceRestrictionConditionTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
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
        $audience = new Audience(
            SAMLAnyURIValue::fromString('urn:x-simplesamlphp:audience'),
        );
        $audienceRestrictionCondition = new AudienceRestrictionCondition([$audience]);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($audienceRestrictionCondition),
        );
    }
}
