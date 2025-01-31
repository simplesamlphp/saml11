<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\samlp;

use DOMDocument;
use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Type\{
    AnyURIValue as SAMLAnyURIValue,
    DateTimeValue as SAMLDateTimeValue,
    StringValue as SAMLStringValue,
};
use SimpleSAML\SAML11\XML\saml\{
    Action,
    Assertion,
    AssertionIDReference,
    AttributeStatement,
    Audience,
    AudienceRestrictionCondition,
    AuthenticationStatement,
    Conditions,
    ConfirmationMethod,
    DoNotCacheCondition,
    Evidence,
    NameIdentifier,
    Subject,
    SubjectConfirmation,
    SubjectConfirmationData,
};
use SimpleSAML\SAML11\XML\samlp\{
    AbstractAuthorizationDecisionQueryType,
    AbstractQueryAbstractType,
    AbstractSamlpElement,
    AbstractSubjectQueryAbstractType,
    AuthorizationDecisionQuery,
};
use SimpleSAML\XML\{Chunk, DOMDocumentFactory};
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XML\Type\{
    Base64BinaryValue,
    IDValue,
    NCNameValue,
    NonNegativeIntegerValue,
    StringValue,
};
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XML\ds\{
    KeyInfo,
    KeyName,
    X509Certificate,
    X509Data,
    X509SubjectName,
};

use function dirname;
use function strval;

/**
 * Tests for AuthorizationDecisionQuery elements.
 *
 * @package simplesamlphp/saml11
 */
#[Group('samlp')]
#[CoversClass(AuthorizationDecisionQuery::class)]
#[CoversClass(AbstractAuthorizationDecisionQueryType::class)]
#[CoversClass(AbstractSubjectQueryAbstractType::class)]
#[CoversClass(AbstractQueryAbstractType::class)]
#[CoversClass(AbstractSamlpElement::class)]
final class AuthorizationDecisionQueryTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /** @var string */
    private static string $certificate;

    /** @var string[] */
    private static array $certData;

    /** @var \DOMDocument $advice */
    //private static DOMDocument $advice;

    /** @var \DOMDocument $statement */
    //private static DOMDocument $statement;

    /** @var \DOMDocument $subjectStatement */
    //private static DOMDocument $subjectStatement;

    /** @var \DOMDocument $authnStatement */
    private static DOMDocument $authnStatement;

    /** @var \DOMDocument $authzDecisionStatement */
    //private static DOMDocument $authzDecisionStatement;

    /** @var \DOMDocument $attributeStatement */
    private static DOMDocument $attributeStatement;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = AuthorizationDecisionQuery::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/samlp_AuthorizationDecisionQuery.xml',
        );

        self::$certificate = str_replace(
            [
                '-----BEGIN CERTIFICATE-----',
                '-----END CERTIFICATE-----',
                '-----BEGIN RSA PUBLIC KEY-----',
                '-----END RSA PUBLIC KEY-----',
                "\r\n",
                "\n",
            ],
            [
                '',
                '',
                '',
                '',
                "\n",
                '',
            ],
            PEMCertificatesMock::getPlainCertificate(PEMCertificatesMock::SELFSIGNED_CERTIFICATE),
        );

        self::$certData = openssl_x509_parse(
            PEMCertificatesMock::getPlainCertificate(PEMCertificatesMock::SELFSIGNED_CERTIFICATE),
        );

//        self::$advice = DOMDocumentFactory::fromFile(
//            dirname(__FILE__, 5) . '/resources/xml/saml_Advice.xml',
//        );

//        self::$statement = DOMDocumentFactory::fromFile(
//            dirname(__FILE__, 5) . '/resources/xml/saml_Statement.xml',
//        );

//        self::$subjectStatement = DOMDocumentFactory::fromFile(
//            dirname(__FILE__, 5) . '/resources/xml/saml_SubjectStatement.xml',
//        );

        self::$authnStatement = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_AuthenticationStatement.xml',
        );

//        self::$authzDecisionStatement = DOMDocumentFactory::fromFile(
//            dirname(__FILE__, 5) . '/resources/xml/saml_AuthorizationDecisionStatement.xml',
//        );

        self::$attributeStatement = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_AttributeStatement.xml',
        );
    }


    // marshalling


    /**
     * Test creating an AuthorizationDecisionQuery from scratch.
     */
    public function testMarshalling(): void
    {
        $scd = new SubjectConfirmationData(
            SAMLStringValue::fromString('phpunit'),
        );

        $keyInfo = new KeyInfo(
            [
                new KeyName(
                    StringValue::fromString('testkey'),
                ),
                new X509Data(
                    [
                        new X509Certificate(
                            Base64BinaryValue::fromString(self::$certificate),
                        ),
                        new X509SubjectName(
                            StringValue::fromString(self::$certData['name']),
                        ),
                    ],
                ),
                new Chunk(DOMDocumentFactory::fromString(
                    '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">some</ssp:Chunk>',
                )->documentElement),
            ],
            IDValue::fromString('fed654'),
        );

        $sc = new SubjectConfirmation(
            [
                new ConfirmationMethod(
                    SAMLAnyURIValue::fromString('_Test1'),
                ),
                new ConfirmationMethod(
                    SAMLAnyURIValue::fromString('_Test2'),
                ),
            ],
            $scd,
            $keyInfo,
        );

        $nameIdentifier = new NameIdentifier(
            SAMLStringValue::fromString('TheNameIDValue'),
            SAMLStringValue::fromString('TheNameQualifier'),
            SAMLAnyURIValue::fromString('urn:the:format'),
        );

        $subject = new Subject($sc, $nameIdentifier);

        $assertionIDReference = new AssertionIDReference(
            NCNameValue::fromString('_Test'),
        );

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
                AttributeStatement::fromXML(self::$attributeStatement->documentElement),
            ],
        );

        $evidence = new Evidence([$assertionIDReference], [$assertion]);

        $authorizationDecisionQuery = new AuthorizationDecisionQuery(
            $subject,
            SAMLAnyURIValue::fromString('urn:some:resource'),
            $evidence,
            [
                new Action(
                    SAMLStringValue::fromString('urn:x-simplesamlphp:action'),
                    SAMLAnyURIValue::fromString('urn:x-simplesamlphp:namespace'),
                ),
            ],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($authorizationDecisionQuery),
        );
    }
}
