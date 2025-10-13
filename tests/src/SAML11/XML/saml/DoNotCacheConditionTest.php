<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\XML\saml\AbstractDoNotCacheConditionType;
use SimpleSAML\SAML11\XML\saml\AbstractSamlElement;
use SimpleSAML\SAML11\XML\saml\DoNotCacheCondition;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;

use function dirname;
use function strval;

/**
 * Tests for DoNotCacheCondition elements.
 *
 * @package simplesamlphp/saml11
 */
#[Group('saml')]
#[CoversClass(DoNotCacheCondition::class)]
#[CoversClass(AbstractDoNotCacheConditionType::class)]
#[CoversClass(AbstractSamlElement::class)]
final class DoNotCacheConditionTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = DoNotCacheCondition::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_DoNotCacheCondition.xml',
        );
    }


    // marshalling


    /**
     * Test creating an DoNotCacheCondition from scratch
     */
    public function testMarshalling(): void
    {
        $DoNotCacheCondition = new DoNotCacheCondition();

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($DoNotCacheCondition),
        );
    }
}
