<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use DateTimeImmutable;
use DOMDocument;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Compat\AbstractContainer;
use SimpleSAML\SAML11\Compat\ContainerSingleton;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\XML\saml\AbstractEvidenceType;
use SimpleSAML\SAML11\XML\saml\AbstractSamlElement;
use SimpleSAML\SAML11\XML\saml\AbstractStatement;
//use SimpleSAML\SAML11\XML\saml\Advice;
use SimpleSAML\SAML11\XML\saml\Assertion;
use SimpleSAML\SAML11\XML\saml\AssertionIDReference;
use SimpleSAML\SAML11\XML\saml\{Attribute, AttributeStatement, AttributeValue};
use SimpleSAML\SAML11\XML\saml\Audience;
use SimpleSAML\SAML11\XML\saml\AuthenticationStatement;
//use SimpleSAML\SAML11\XML\saml\AuthorizationDecisionStatement;
use SimpleSAML\SAML11\XML\saml\AuthorityBinding;
use SimpleSAML\SAML11\XML\saml\Conditions;
use SimpleSAML\SAML11\XML\saml\Evidence;
use SimpleSAML\SAML11\XML\saml\ConfirmationMethod;
use SimpleSAML\SAML11\XML\saml\NameIdentifier;
use SimpleSAML\SAML11\XML\saml\{Subject, SubjectConfirmation, SubjectConfirmationData, SubjectLocality};
use SimpleSAML\Test\SAML11\{CustomCondition, CustomStatement, CustomSubjectStatement};
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XML\ds\{KeyInfo, KeyName};
use SimpleSAML\XMLSecurity\XML\ds\{X509Certificate, X509CertificateName, X509Data, X509SubjectName};

use function dirname;
use function strval;

/**
 * Tests for Evidence elements.
 *
 * @package simplesamlphp/saml11
 */
#[CoversClass(Evidence::class)]
#[CoversClass(AbstractEvidenceType::class)]
#[CoversClass(AbstractSamlElement::class)]
final class EvidenceTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /** @var string */
    private static string $certificate;

    /** @var string[] */
    private static array $certData;

    /** @var \SimpleSAML\SAML11\Compat\AbstractContainer */
    private static AbstractContainer $containerBackup;

    /** @var \DOMDocument $conditions */
    private static DOMDocument $conditions;

    /** @var \DOMDocument $advice */
    //private static DOMDocument $advice;

    /** @var \DOMDocument $authzDecisionStatement */
    //private static DOMDocument $authzDecisionStatement;

    /** @var \DOMDocument $statement */
    private static DOMDocument $statement;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$containerBackup = ContainerSingleton::getInstance();

        self::$schemaFile = dirname(__FILE__, 5) . '/resources/schemas/simplesamlphp.xsd';

        self::$testedClass = Evidence::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Evidence.xml',
        );

        $container = clone self::$containerBackup;
        $container->registerExtensionHandler(CustomCondition::class);
        $container->registerExtensionHandler(CustomStatement::class);
        $container->registerExtensionHandler(CustomSubjectStatement::class);
        ContainerSingleton::setContainer($container);

        self::$conditions = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Conditions.xml',
        );

//        self::$advice = DOMDocumentFactory::fromFile(
//            dirname(__FILE__, 5) . '/resources/xml/saml_Advice.xml',
//        );

        self::$statement = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Statement.xml',
        );

//        self::$authzDecisionStatement = DOMDocumentFactory::fromFile(
//            dirname(__FILE__, 5) . '/resources/xml/saml_AuthorizationDecisionStatement.xml',
//        );

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
    }


    /**
     */
    public static function tearDownAfterClass(): void
    {
        ContainerSingleton::setContainer(self::$containerBackup);
    }


    // marshalling


    /**
     * Test creating an Assertion from scratch
     */
    public function testMarshalling(): void
    {
        // Create SubjectStatement
        $subjectStatement = $this->createSubjectStatement('EvidenceSubjectStatementID');

        // Create AuthenticationStatement
        $authenticationStatement = $this->createAuthenticationStatement('EvidenceAuthenticationStatementID');

        // Create AttributeStatement
        $attributeStatement = $this->createAttributeStatement('EvidenceAttributeStatementID');

        $assertionIDReference = new AssertionIDReference('_Test');

        $assertion = new Assertion(
            'EvidenceAssertionID',
            'urn:x-simplesamlphp:phpunit',
            new DateTimeImmutable('2023-01-24T09:42:26Z'),
            Conditions::fromXML(self::$conditions->documentElement),
            null, // advice
            [
                AbstractStatement::fromXML(self::$statement->documentElement),
                $subjectStatement,
                $authenticationStatement,
                //null, // authzDecisionStatement
                $attributeStatement,
            ],
        );

        $evidence = new Evidence([$assertionIDReference], [$assertion]);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($evidence),
        );
    }


    /**
     * @param string $id
     * @return \SimpleSAML\Test\SAML11\CustomSubjectStatement
     */
    private function createSubjectStatement(string $id): CustomSubjectStatement
    {
        // Create SubjectStatement
        $scd = new SubjectConfirmationData(2);

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
            $id,
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
        $audience = new Audience('urn:x-simplesamlphp:audience');
        return new CustomSubjectStatement($subject, [$audience]);
    }


    /**
     * @param string $id
     * @return \SimpleSAML\SAML11\XML\saml\AuthenticationStatement
     */
    private function createAuthenticationStatement(string $id): AuthenticationStatement
    {
        $scd = new SubjectConfirmationData(2);

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
            $id,
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

        $subjectLocality = new SubjectLocality('127.0.0.1', 'simplesamlphp.org');
        $authorityBinding = new AuthorityBinding(
            'samlp:AttributeQuery',
            'urn:x-simplesamlphp:location',
            'urn:x-simplesamlphp:binding',
        );

        return new AuthenticationStatement(
            $subject,
            C::AC_PASSWORD,
            new DateTimeImmutable('2023-01-24T09:42:26Z'),
            $subjectLocality,
            [$authorityBinding],
        );
    }


    /**
     * @param string $id
     * @return \SimpleSAML\SAML11\XML\saml\AttributeStatement
     */
    private function createAttributeStatement(string $id): AttributeStatement
    {
        $scd = new SubjectConfirmationData(2);

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
            $id,
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

        $attribute = new Attribute(
            'TheName',
            'https://example.org/',
            [new AttributeValue('FirstValue'), new AttributeValue('SecondValue')],
        );

        return new AttributeStatement(
            $subject,
            [$attribute],
        );
    }
}
