<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use DateTimeImmutable;
use DOMDocument;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Constants as C;
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

use function dirname;
use function strval;

/**
 * Tests for Response elements.
 *
 * @package simplesamlphp/saml11
 */
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
        self::$schemaFile = dirname(__FILE__, 6) . '/resources/schemas/oasis-sstc-saml-schema-protocol-1.1.xsd';

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
        $audience = new Audience('urn:x-simplesamlphp:audience');
        $audienceRestrictionCondition = new AudienceRestrictionCondition([$audience]);

        $doNotCacheCondition = new DoNotCacheCondition();

        $conditions = new Conditions(
            [$audienceRestrictionCondition],
            [$doNotCacheCondition],
            [],
            new DateTimeImmutable('2023-01-24T09:42:26Z'),
            new DateTimeImmutable('2023-01-24T09:47:26Z'),
        );

        $assertion = new Assertion(
            '_abc123',
            'urn:x-simplesamlphp:phpunit',
            new DateTimeImmutable('2023-01-24T09:42:26Z'),
            $conditions,
            null, // advice
            [
                AuthenticationStatement::fromXML(self::$authnStatement->documentElement),
            ],
        );

        $status = new Status(
            new StatusCode(
                C::STATUS_RESPONDER,
                [
                    new StatusCode(
                        C::STATUS_REQUEST_DENIED,
                    ),
                ],
            ),
            new StatusMessage('Something went wrong'),
        );

        $response = new Response(
            'def456',
            $status,
            [$assertion],
            1,
            1,
            new DateTimeImmutable('2023-01-24T09:42:26Z'),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($response),
        );
    }
}
