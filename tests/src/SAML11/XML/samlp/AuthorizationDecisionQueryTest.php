<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use DateTimeImmutable;
use DOMDocument;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\XML\saml\Action;
use SimpleSAML\SAML11\XML\saml\Assertion;
use SimpleSAML\SAML11\XML\saml\AssertionIDReference;
use SimpleSAML\SAML11\XML\saml\AttributeStatement;
use SimpleSAML\SAML11\XML\saml\Audience;
use SimpleSAML\SAML11\XML\saml\AudienceRestrictionCondition;
use SimpleSAML\SAML11\XML\saml\AuthenticationStatement;
use SimpleSAML\SAML11\XML\saml\Conditions;
use SimpleSAML\SAML11\XML\saml\ConfirmationMethod;
use SimpleSAML\SAML11\XML\saml\DoNotCacheCondition;
use SimpleSAML\SAML11\XML\saml\Evidence;
use SimpleSAML\SAML11\XML\saml\NameIdentifier;
use SimpleSAML\SAML11\XML\saml\Subject;
use SimpleSAML\SAML11\XML\saml\SubjectConfirmation;
use SimpleSAML\SAML11\XML\saml\SubjectConfirmationData;
use SimpleSAML\SAML11\XML\samlp\AbstractAuthorizationDecisionQueryType;
use SimpleSAML\SAML11\XML\samlp\AbstractQueryAbstractType;
use SimpleSAML\SAML11\XML\samlp\AbstractSamlpElement;
use SimpleSAML\SAML11\XML\samlp\AbstractSubjectQueryAbstractType;
use SimpleSAML\SAML11\XML\samlp\AuthorizationDecisionQuery;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XML\ds\KeyInfo;
use SimpleSAML\XMLSecurity\XML\ds\KeyName;
use SimpleSAML\XMLSecurity\XML\ds\X509Certificate;
use SimpleSAML\XMLSecurity\XML\ds\X509Data;
use SimpleSAML\XMLSecurity\XML\ds\X509SubjectName;

use function dirname;
use function strval;

/**
 * Tests for AuthorizationDecisionQuery elements.
 *
 * @package simplesamlphp/saml11
 */
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
        self::$schemaFile = dirname(__FILE__, 6) . '/resources/schemas/oasis-sstc-saml-schema-protocol-1.1.xsd';

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
        $scd = new SubjectConfirmationData('phpunit');

        $keyInfo = new KeyInfo(
            [
                new KeyName('testkey'),
                new X509Data(
                    [
                        new X509Certificate(self::$certificate),
                        new X509SubjectName(self::$certData['name']),
                    ],
                ),
                new Chunk(DOMDocumentFactory::fromString(
                    '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">some</ssp:Chunk>',
                )->documentElement),
            ],
            'fed654',
        );

        $sc = new SubjectConfirmation(
            [new ConfirmationMethod('_Test1'), new ConfirmationMethod('_Test2')],
            $scd,
            $keyInfo,
        );

        $nameIdentifier = new NameIdentifier(
            'TheNameIDValue',
            'TheNameQualifier',
            'urn:the:format',
        );

        $subject = new Subject($sc, $nameIdentifier);

        $assertionIDReference = new AssertionIDReference('_Test');

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
                AttributeStatement::fromXML(self::$attributeStatement->documentElement),
            ],
        );

        $evidence = new Evidence([$assertionIDReference], [$assertion]);

        $authorizationDecisionQuery = new AuthorizationDecisionQuery(
            $subject,
            'urn:some:resource',
            $evidence,
            [
                new Action('urn:x-simplesamlphp:action', 'urn:x-simplesamlphp:namespace'),
            ],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($authorizationDecisionQuery),
        );
    }
}
