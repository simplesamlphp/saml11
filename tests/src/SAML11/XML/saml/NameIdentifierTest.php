<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\XML\saml\NameIdentifier;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\SAML11\XML\saml\NameIdentifierTest
 *
 * @covers \SimpleSAML\SAML11\XML\saml\NameIdentifier
 * @covers \SimpleSAML\SAML11\XML\saml\AbstractNameIdentifierType
 * @covers \SimpleSAML\SAML11\XML\saml\AbstractSamlElement
 *
 * @package simplesamlphp/saml11
 */
final class NameIdentifierTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$schemaFile = dirname(__FILE__, 6) . '/resources/schemas/oasis-sstc-saml-schema-assertion-1.1.xsd';

        self::$testedClass = NameIdentifier::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_NameIdentifier.xml',
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $nameIdentifier = new NameIdentifier(
            'TheNameIDValue',
            'TheNameQualifier',
            'urn:the:format',
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($nameIdentifier),
        );
    }
}
