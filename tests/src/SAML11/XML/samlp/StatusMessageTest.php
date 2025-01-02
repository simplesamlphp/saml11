<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\samlp;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\XML\samlp\AbstractSamlpElement;
use SimpleSAML\SAML11\XML\samlp\StatusMessage;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\SAML11\XML\samlp\StatusMessageTest
 *
 * @package simplesamlphp/SAML11
 */
#[Group('samlp')]
#[CoversClass(StatusMessage::class)]
#[CoversClass(AbstractSamlpElement::class)]
final class StatusMessageTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$schemaFile = dirname(__FILE__, 6) . '/resources/schemas/oasis-sstc-saml-schema-protocol-1.1.xsd';

        self::$testedClass = StatusMessage::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/samlp_StatusMessage.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $statusMessage = new StatusMessage('Something went wrong');

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($statusMessage),
        );
    }
}
