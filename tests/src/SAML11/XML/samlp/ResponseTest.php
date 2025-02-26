<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\samlp;

use DOMDocument;
use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Type\{SAMLAnyURIValue, SAMLDateTimeValue, SAMLStringValue};
use SimpleSAML\SAML11\XML\saml\{
    Assertion,
    //AttributeStatement,
    Audience,
    AudienceRestrictionCondition,
    AuthenticationStatement,
    Conditions,
    DoNotCacheCondition,
};
use SimpleSAML\SAML11\XML\samlp\{
    AbstractResponseAbstractType,
    AbstractSamlpElement,
    Response,
    Status,
    StatusCode,
    //StatusDetail,
    StatusMessage,
};
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XML\Type\{IDValue, NonNegativeIntegerValue, QNameValue};

use function dirname;
use function strval;

/**
 * Tests for Response elements.
 *
 * @package simplesamlphp/saml11
 */
#[Group('samlp')]
#[CoversClass(Response::class)]
#[CoversClass(AbstractResponseAbstractType::class)]
#[CoversClass(AbstractSamlpElement::class)]
final class ResponseTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /** @var \DOMDocument $authnStatement */
    private static DOMDocument $authnStatement;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = Response::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/samlp_Response.xml',
        );

        self::$authnStatement = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_AuthenticationStatement.xml',
        );
    }


    // marshalling


    /**
     * Test creating an Response from scratch.
     */
    public function testMarshalling(): void
    {
        $audience = new Audience(
            SAMLAnyURIValue::fromString('urn:x-simplesamlphp:audience'),
        );
        $audienceRestrictionCondition = new AudienceRestrictionCondition([$audience]);

        $doNotCacheCondition = new DoNotCacheCondition();

        $conditions = new Conditions(
            [$audienceRestrictionCondition],
            [$doNotCacheCondition],
            [],
            SAMLDateTimeValue::fromString('2023-01-24T09:42:26Z'),
            SAMLDateTimeValue::fromString('2023-01-24T09:47:26Z'),
        );

        $assertion = new Assertion(
            NonNegativeIntegerValue::fromString('1'),
            NonNegativeIntegerValue::fromString('1'),
            IDValue::fromString('_abc123'),
            SAMLStringValue::fromString('urn:x-simplesamlphp:phpunit'),
            SAMLDateTimeValue::fromString('2023-01-24T09:42:26Z'),
            $conditions,
            null, // advice
            [
                AuthenticationStatement::fromXML(self::$authnStatement->documentElement),
            ],
        );

        $status = new Status(
            new StatusCode(
                QNameValue::fromString('{' . C::NS_SAMLP . '}' . C::STATUS_RESPONDER),
                [
                    new StatusCode(
                        QNameValue::fromString('{' . C::NS_SAMLP . '}' . C::STATUS_REQUEST_DENIED),
                    ),
                ],
            ),
            new StatusMessage(
                SAMLStringValue::fromString('Something went wrong'),
            ),
        );

        $response = new Response(
            NonNegativeIntegerValue::fromString('1'),
            NonNegativeIntegerValue::fromString('1'),
            IDValue::fromString('def456'),
            $status,
            SAMLDateTimeValue::fromString('2023-01-24T09:42:26Z'),
            [$assertion],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($response),
        );
    }
}
