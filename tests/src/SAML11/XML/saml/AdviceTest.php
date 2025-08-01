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
    AbstractAdviceType,
    AbstractSamlElement,
    AbstractStatement,
    AbstractSubjectStatement,
    Advice,
    Assertion,
    AssertionIDReference,
    Attribute,
    AttributeStatement,
    AttributeValue,
    Audience,
    AuthenticationStatement,
    AuthorityBinding,
    //AuthorizationDecisionStatement,
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
use SimpleSAML\XMLSchema\Type\{
    Base64BinaryValue,
    IDValue,
    IntegerValue,
    NCNameValue,
    NonNegativeIntegerValue,
    QNameValue,
    StringValue
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
 * Class \SimpleSAML\SAML11\XML\saml\AdviceTest
 *
 * @package simplesamlphp/saml11
 */
#[Group('saml')]
#[CoversClass(Advice::class)]
#[CoversClass(AbstractAdviceType::class)]
#[CoversClass(AbstractSamlElement::class)]
final class AdviceTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /** @var string */
    private static string $certificate;

    /** @var string[] */
    private static array $certData;

    /** @var \SimpleSAML\SAML11\Compat\AbstractContainer */
    private static AbstractContainer $containerBackup;

    /** @var \SimpleSAML\XML\Chunk $chunk */
    private static Chunk $chunk;

    /** @var \DOMDocument $conditions */
    private static DOMDocument $conditions;

    /** @var \DOMDocument $statement */
    private static DOMDocument $statement;

    /** @var \DOMDocument $subjectStatement */
    private static DOMDocument $subjectStatement;

    /** @var \DOMDocument $authnStatement */
    private static DOMDocument $authnStatement;

    /** @var \DOMDocument $authzDecisionStatement */
//    private static DOMDocument $authzDecisionStatement;

    /** @var \DOMDocument $attributeStatement */
    private static DOMDocument $attributeStatement;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$containerBackup = ContainerSingleton::getInstance();

        self::$schemaFile = dirname(__FILE__, 5) . '/resources/schemas/simplesamlphp.xsd';

        self::$testedClass = Advice::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Advice.xml',
        );

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

//        self::$authzDecisionStatement = DOMDocumentFactory::fromFile(
//            dirname(__FILE__, 5) . '/resources/xml/saml_AuthorizationDecisionStatement.xml',
//        );

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

        $container = clone self::$containerBackup;
        $container->registerExtensionHandler(CustomCondition::class);
        $container->registerExtensionHandler(CustomStatement::class);
        $container->registerExtensionHandler(CustomSubjectStatement::class);
        ContainerSingleton::setContainer($container);
    }


    /**
     */
    public static function tearDownAfterClass(): void
    {
        ContainerSingleton::setContainer(self::$containerBackup);
    }


    /**
     */
    public function testMarshalling(): void
    {
        $assertionIDReference = new AssertionIDReference(
            NCNameValue::fromString('_Test'),
        );

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
            IDValue::fromString('AdviceSubjectStatementID'),
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
        $subjectStatement = new CustomSubjectStatement($subject, [$audience]);

        // Create AuthenticationStatement
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
            IDValue::fromString('AdviceAuthenticationStatementID'),
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

        $authenticationStatement = new AuthenticationStatement(
            $subject,
            SAMLAnyURIValue::fromString(C::AC_PASSWORD),
            SAMLDateTimeValue::fromString('2023-01-24T09:42:26Z'),
            $subjectLocality,
            [$authorityBinding],
        );

        // Create AttributeStatement
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
            IDValue::fromString('AdviceAttributeStatementID'),
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

        $attributeStatement = new AttributeStatement(
            $subject,
            [$attribute],
        );

        // Create assertion
        $assertion = new Assertion(
            NonNegativeIntegerValue::fromString('1'),
            NonNegativeIntegerValue::fromString('1'),
            IDValue::fromString('AdviceAssertionID'),
            SAMLStringValue::fromString('urn:x-simplesamlphp:phpunit'),
            SAMLDateTimeValue::fromString('2023-01-24T09:42:26Z'),
            Conditions::fromXML(self::$conditions->documentElement),
            null, // null
            [
                AbstractStatement::fromXML(self::$statement->documentElement),
                $subjectStatement,
                $authenticationStatement,
                //null, // authzDecisionStatement
                $attributeStatement,
            ],
        );

        $advice = new Advice(
            [$assertionIDReference],
            [$assertion],
            [self::$chunk],
        );

        $assertion = new Assertion(
            NonNegativeIntegerValue::fromString('1'),
            NonNegativeIntegerValue::fromString('1'),
            IDValue::fromString('_abc123'),
            SAMLStringValue::fromString('urn:x-simplesamlphp:phpunit'),
            SAMLDateTimeValue::fromString('2023-01-24T09:42:26Z'),
            Conditions::fromXML(self::$conditions->documentElement),
            $advice,
            [
                AbstractStatement::fromXML(self::$statement->documentElement),
                AbstractSubjectStatement::fromXML(self::$subjectStatement->documentElement),
                AuthenticationStatement::fromXML(self::$authnStatement->documentElement),
                //null, // authzDecisionStatement
                AttributeStatement::fromXML(self::$attributeStatement->documentElement),
            ],
        );

        $advice = new Advice(
            [$assertionIDReference],
            [$assertion],
            [self::$chunk],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($advice),
        );
    }

    /**
     */
    public function testMarshallingEmpty(): void
    {
        $advice = new Advice();
        $this->assertEquals(
            '<saml:Advice xmlns:saml="urn:oasis:names:tc:SAML:1.0:assertion"/>',
            strval($advice),
        );
        $this->assertTrue($advice->isEmptyElement());
    }
}
