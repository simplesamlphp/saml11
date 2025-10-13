<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\samlp;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\XML\samlp\AbstractSamlpElement;
use SimpleSAML\SAML11\XML\samlp\StatusCode;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Type\QNameValue;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\SAML11\XML\samlp\StatusCodeTest
 *
 * @package simplesamlphp/saml11
 */
#[Group('samlp')]
#[CoversClass(StatusCode::class)]
#[CoversClass(AbstractSamlpElement::class)]
final class StatusCodeTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = StatusCode::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/samlp_StatusCode.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {

        $statusCode = new StatusCode(
            QNameValue::fromString('{' . C::NS_SAMLP . '}' . C::STATUS_RESPONDER),
            [
                new StatusCode(
                    QNameValue::fromString('{' . C::NS_SAMLP . '}' . C::STATUS_REQUEST_DENIED),
                ),
            ],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($statusCode),
        );
    }
}
