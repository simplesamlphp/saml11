<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\XML\saml\{AbstractSamlElement, AssertionIDReference};
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSchema\Type\IDValue;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\SAML11\XML\saml\AssertionIDReferenceTest
 *
 * @package simplesamlphp/saml11
 */
#[Group('saml')]
#[CoversClass(AssertionIDReference::class)]
#[CoversClass(AbstractSamlElement::class)]
final class AssertionIDReferenceTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = AssertionIDReference::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_AssertionIDReference.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $assertionIDReference = new AssertionIDReference(
            IDValue::fromString('_Test'),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($assertionIDReference),
        );
    }
}
