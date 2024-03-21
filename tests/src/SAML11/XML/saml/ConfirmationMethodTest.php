<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\XML\saml\AbstractSamlElement;
use SimpleSAML\SAML11\XML\saml\ConfirmationMethod;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\SAML11\XML\saml\ConfirmationMethodTest
 *
 * @package simplesamlphp/saml11
 */
#[CoversClass(ConfirmationMethod::class)]
#[CoversClass(AbstractSamlElement::class)]
final class ConfirmationMethodTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$schemaFile = dirname(__FILE__, 6) . '/resources/schemas/oasis-sstc-saml-schema-assertion-1.1.xsd';

        self::$testedClass = ConfirmationMethod::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_ConfirmationMethod.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $confirmationMethod = new ConfirmationMethod('_Test');

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($confirmationMethod),
        );
    }
}
