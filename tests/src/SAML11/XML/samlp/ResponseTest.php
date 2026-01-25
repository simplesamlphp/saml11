<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\samlp;

use DOMDocument;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Type\SAMLDateTimeValue;
use SimpleSAML\SAML11\Type\SAMLStringValue;
use SimpleSAML\SAML11\XML\saml\Assertion;
//use SimpleSAML\SAML11\XML\saml\AttributeStatement;
use SimpleSAML\SAML11\XML\saml\Audience;
use SimpleSAML\SAML11\XML\saml\AudienceRestrictionCondition;
use SimpleSAML\SAML11\XML\saml\AuthenticationStatement;
use SimpleSAML\SAML11\XML\saml\Conditions;
use SimpleSAML\SAML11\XML\saml\DoNotCacheCondition;
use SimpleSAML\SAML11\XML\samlp\AbstractResponseAbstractType;
use SimpleSAML\SAML11\XML\samlp\AbstractSamlpElement;
use SimpleSAML\SAML11\XML\samlp\Response;
use SimpleSAML\SAML11\XML\samlp\Status;
use SimpleSAML\SAML11\XML\samlp\StatusCode;
//use SimpleSAML\SAML11\XML\samlp\StatusDetail;
use SimpleSAML\SAML11\XML\samlp\StatusMessage;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Type\IDValue;
use SimpleSAML\XMLSchema\Type\NonNegativeIntegerValue;
use SimpleSAML\XMLSchema\Type\QNameValue;

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
        $audience = Audience::fromString('urn:x-simplesamlphp:audience');
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
            StatusMessage::fromString('Something went wrong'),
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
