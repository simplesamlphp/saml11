<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\XML\saml\DoNotCacheCondition;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;

use function dirname;
use function strval;

/**
 * Tests for DoNotCacheCondition elements.
 *
 * @covers \SimpleSAML\SAML11\XML\saml\DoNotCacheCondition
 * @covers \SimpleSAML\SAML11\XML\saml\AbstractConditionType
 * @covers \SimpleSAML\SAML11\XML\saml\AbstractSamlElement
 *
 * @package simplesamlphp/saml11
 */
final class DoNotCacheConditionTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$schemaFile = dirname(__FILE__, 6) . '/resources/schemas/oasis-sstc-saml-schema-assertion-1.1.xsd';

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
