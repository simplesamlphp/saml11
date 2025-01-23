<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\XML\saml\{AbstractActionType, AbstractSamlElement, Action};
use SimpleSAML\SAML11\Type\{AnyURIValue, StringValue};
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};

use function dirname;
use function strval;

/**
 * Tests for Action elements.
 *
 * @package simplesamlphp/saml11
 */
#[Group('saml')]
#[CoversClass(Action::class)]
#[CoversClass(AbstractActionType::class)]
#[CoversClass(AbstractSamlElement::class)]
final class ActionTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = Action::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Action.xml',
        );
    }


    // marshalling


    /**
     * Test creating an Action from scratch
     */
    public function testMarshalling(): void
    {
        $action = new Action(
            StringValue::fromString('urn:x-simplesamlphp:action'),
            AnyURIValue::fromString('urn:x-simplesamlphp:namespace'),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($action),
        );
    }
}
