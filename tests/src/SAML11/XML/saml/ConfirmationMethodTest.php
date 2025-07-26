<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Type\SAMLAnyURIValue;
use SimpleSAML\SAML11\XML\saml\{AbstractSamlElement, ConfirmationMethod};
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\SAML11\XML\saml\ConfirmationMethodTest
 *
 * @package simplesamlphp/saml11
 */
#[Group('saml')]
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
        self::$testedClass = ConfirmationMethod::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_ConfirmationMethod.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $confirmationMethod = new ConfirmationMethod(
            SAMLAnyURIValue::fromString('_Test'),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($confirmationMethod),
        );
    }
}
