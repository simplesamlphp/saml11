<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use DOMDocument;
use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Compat\{AbstractContainer, ContainerSingleton};
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Type\{SAMLAnyURIValue, SAMLDateTimeValue, SAMLStringValue};
use SimpleSAML\SAML11\XML\saml\{
    AbstractAssertionType,
    AbstractAttributeStatementType,
    AbstractAuthenticationStatementType,
    AbstractAuthorizationDecisionStatementType,
    AbstractSamlElement,
    AbstractStatement,
    AbstractStatementType,
    AbstractSubjectStatement,
    AbstractSubjectStatementType,
    Advice,
    Assertion,
    AssertionIDReference,
    Attribute,
    AttributeStatement,
    AttributeValue,
    Audience,
    AuthenticationStatement,
    AuthorityBinding,
    AuthorizationDecisionStatement,
    Conditions,
    ConfirmationMethod,
    NameIdentifier,
    Subject,
    SubjectConfirmation,
    SubjectConfirmationData,
    SubjectLocality,
};
use SimpleSAML\Test\SAML11\{CustomCondition, CustomStatement, CustomSubjectStatement};
use SimpleSAML\XML\{Chunk, DOMDocumentFactory};
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XML\Type\{
    Base64BinaryValue,
    IDValue,
    IntegerValue,
    NCNameValue,
    NonNegativeIntegerValue,
    QNameValue,
    StringValue,
};
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XML\ds\{KeyInfo, KeyName};
use SimpleSAML\XMLSecurity\XML\ds\{X509Certificate, X509CertificateName, X509Data, X509SubjectName};

use function dirname;
use function strval;

/**
 * Tests for Assertion elements.
 *
 * @package simplesamlphp/saml11
 */
#[Group('saml')]
#[CoversClass(Assertion::class)]
#[CoversClass(AbstractAssertionType::class)]
#[CoversClass(AbstractSamlElement::class)]
final class AssertionTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /** @var string */
    private static string $certificate;

    /** @var string[] */
    private static array $certData;

    /** @var \SimpleSAML\XML\Chunk $chunk */
    private static Chunk $chunk;

    /** @var \SimpleSAML\SAML11\Compat\AbstractContainer */
    private static AbstractContainer $containerBackup;

    /** @var \DOMDocument $conditions */
    private static DOMDocument $conditions;

    /** @var \DOMDocument $action */
    //private static DOMDocument $action;

    /** @var \DOMDocument $statement */
    private static DOMDocument $statement;

    /** @var \DOMDocument $subjectStatement */
    private static DOMDocument $subjectStatement;

    /** @var \DOMDocument $authnStatement */
    private static DOMDocument $authnStatement;

    /** @var \DOMDocument $authzDecisionStatement */
    private static DOMDocument $authzDecisionStatement;

    /** @var \DOMDocument $attributeStatement */
    private static DOMDocument $attributeStatement;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$containerBackup = ContainerSingleton::getInstance();

        self::$schemaFile = dirname(__FILE__, 5) . '/resources/schemas/simplesamlphp.xsd';

        self::$testedClass = Assertion::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Assertion.xml',
        );

        $container = clone self::$containerBackup;
        $container->registerExtensionHandler(CustomCondition::class);
        $container->registerExtensionHandler(CustomStatement::class);
        $container->registerExtensionHandler(CustomSubjectStatement::class);
        ContainerSingleton::setContainer($container);

        self::$conditions = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Conditions.xml',
        );

        self::$statement = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Statement.xml',
        );

        self::$subjectStatement = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_SubjectStatement.xml',
        );

        self::$authnStatement = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_AuthenticationStatement.xml',
        );

        self::$authzDecisionStatement = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_AuthorizationDecisionStatement.xml',
        );

        self::$attributeStatement = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_AttributeStatement.xml',
        );

        self::$chunk = new Chunk(DOMDocumentFactory::fromString(
            '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">some</ssp:Chunk>',
        )->documentElement);

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
        $subjectStatement = $this->createSubjectStatement('AdviceSubjectStatementID_2');

        // Create AuthenticationStatement
        $authenticationStatement = $this->createAuthenticationStatement('AdviceAuthenticationStatementID_2');

        // Create AttributeStatement
        $attributeStatement = $this->createAttributeStatement('AdviceAttributeStatementID_2');

        // Create inner assertion
        $assertion = new Assertion(
            NonNegativeIntegerValue::fromString('1'),
            NonNegativeIntegerValue::fromString('1'),
            IDValue::fromString('AdviceAssertionID_2'),
            SAMLStringValue::fromString('urn:x-simplesamlphp:phpunit'),
            SAMLDateTimeValue::fromString('2023-01-24T09:42:26Z'),
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

        // Create middle assertion
        $assertionIDReference = new AssertionIDReference(
            NCNameValue::fromString('_Test'),
        );
        $advice = new Advice(
            [$assertionIDReference],
            [$assertion],
            [self::$chunk],
        );

        // Create SubjectStatement
        $subjectStatement = $this->createSubjectStatement('AdviceSubjectStatementID');

        // Create AuthenticationStatement
        $authenticationStatement = $this->createAuthenticationStatement('AdviceAuthenticationStatementID');

        // Create AttributeStatement
        $attributeStatement = $this->createAttributeStatement('AdviceAttributeStatementID');

        $assertion = new Assertion(
            NonNegativeIntegerValue::fromString('1'),
            NonNegativeIntegerValue::fromString('1'),
            IDValue::fromString('AdviceAssertionID'),
            SAMLStringValue::fromString('urn:x-simplesamlphp:phpunit'),
            SAMLDateTimeValue::fromString('2023-01-24T09:42:26Z'),
            Conditions::fromXML(self::$conditions->documentElement),
            $advice,
            [
                AbstractStatement::fromXML(self::$statement->documentElement),
                $subjectStatement,
                $authenticationStatement,
                //null, // authzDecisionStatement
                $attributeStatement,
            ],
        );

        // Create outer assertion
        $advice = new Advice(
            [$assertionIDReference],
            [$assertion],
            [self::$chunk],
        );

        $assertion = new Assertion(
            NonNegativeIntegerValue::fromString('1'),
            NonNegativeIntegerValue::fromString('1'),
            IDValue::fromString('AssertionID'),
            SAMLStringValue::fromString('urn:x-simplesamlphp:phpunit'),
            SAMLDateTimeValue::fromString('2023-01-24T09:42:26Z'),
            Conditions::fromXML(self::$conditions->documentElement),
            $advice,
            [
                AbstractStatement::fromXML(self::$statement->documentElement),
                AbstractSubjectStatement::fromXML(self::$subjectStatement->documentElement),
                AuthenticationStatement::fromXML(self::$authnStatement->documentElement),
                AuthorizationDecisionStatement::fromXML(self::$authzDecisionStatement->documentElement),
                AttributeStatement::fromXML(self::$attributeStatement->documentElement),
            ],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($assertion),
        );
    }


    /**
     * Test getters for diffent types of statements
     */
    public function testStatementGetters(): void
    {
        $assertion = Assertion::fromXML(self::$xmlRepresentation->documentElement);

        $statements = $assertion->getStatements();
        $this->assertContainsOnlyInstancesOf(AbstractStatementType::class, $statements);
        $this->assertCount(1, $statements);

        $subjectStatements = $assertion->getSubjectStatements();
        $this->assertContainsOnlyInstancesOf(AbstractSubjectStatementType::class, $subjectStatements);
        $this->assertCount(1, $subjectStatements);

        $authnStatements = $assertion->getAuthenticationStatements();
        $this->assertContainsOnlyInstancesOf(AbstractAuthenticationStatementType::class, $authnStatements);
        $this->assertCount(1, $authnStatements);

        $authzDecisionStatements = $assertion->getAuthorizationDecisionStatements();
        $this->assertContainsOnlyInstancesOf(
            AbstractAuthorizationDecisionStatementType::class,
            $authzDecisionStatements,
        );
        $this->assertCount(1, $authzDecisionStatements);

        $attrStatements = $assertion->getattributeStatements();
        $this->assertContainsOnlyInstancesOf(AbstractAttributeStatementType::class, $attrStatements);
        $this->assertCount(1, $attrStatements);
    }


    /**
     * @param string $id
     * @return \SimpleSAML\Test\SAML11\CustomSubjectStatement
     */
    private function createSubjectStatement(string $id): CustomSubjectStatement
    {
        // Create SubjectStatement
        $scd = new SubjectConfirmationData(
            IntegerValue::fromString('2'),
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
            IDValue::fromString($id),
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
        $audience = new Audience(
            SAMLAnyURIValue::fromString('urn:x-simplesamlphp:audience'),
        );

        return new CustomSubjectStatement($subject, [$audience]);
    }


    /**
     * @param string $id
     * @return \SimpleSAML\SAML11\XML\saml\AuthenticationStatement
     */
    private function createAuthenticationStatement(string $id): AuthenticationStatement
    {
        $scd = new SubjectConfirmationData(
            IntegerValue::fromString('2'),
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
            IDValue::fromString($id),
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

        $subjectLocality = new SubjectLocality(
            SAMLStringValue::fromString('127.0.0.1'),
            SAMLStringValue::fromString('simplesamlphp.org'),
        );

        $authorityBinding = new AuthorityBinding(
            QNameValue::fromString('{' . C::NS_SAMLP . '}samlp:AttributeQuery'),
            SAMLAnyURIValue::fromString('urn:x-simplesamlphp:location'),
            SAMLAnyURIValue::fromString('urn:x-simplesamlphp:binding'),
        );

        return new AuthenticationStatement(
            $subject,
            SAMLAnyURIValue::fromString(C::AC_PASSWORD),
            SAMLDateTimeValue::fromString('2023-01-24T09:42:26Z'),
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
        $scd = new SubjectConfirmationData(
            IntegerValue::fromString('2'),
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
            IDValue::fromString($id),
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

        $attribute = new Attribute(
            SAMLStringValue::fromString('TheName'),
            SAMLAnyURIValue::fromString('https://example.org/'),
            [
                new AttributeValue(
                    SAMLStringValue::fromString('FirstValue'),
                ),
                new AttributeValue(
                    SAMLStringValue::fromString('SecondValue'),
                ),
            ],
        );

        return new AttributeStatement(
            $subject,
            [$attribute],
        );
    }
}
