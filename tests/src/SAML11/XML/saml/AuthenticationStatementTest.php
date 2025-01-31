<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Type\{
    AnyURIValue as SAMLAnyURIValue,
    DateTimeValue as SAMLDateTimeValue,
    StringValue as SAMLStringValue,
};
use SimpleSAML\SAML11\Utils\XPath;
use SimpleSAML\SAML11\XML\saml\{
    AbstractAuthenticationStatementType,
    AbstractSamlElement,
    AuthenticationStatement,
    AuthorityBinding,
    ConfirmationMethod,
    NameIdentifier,
    Subject,
    SubjectConfirmation,
    SubjectConfirmationData,
    SubjectLocality,
};
use SimpleSAML\XML\{Chunk, DOMDocumentFactory};
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XML\Type\{Base64BinaryValue, IDValue, IntegerValue, QNameValue, StringValue};
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
 * Tests for AuthenticationStatement elements.
 *
 * @package simplesamlphp/saml11
 */
#[Group('saml')]
#[CoversClass(AuthenticationStatement::class)]
#[CoversClass(AbstractAuthenticationStatementType::class)]
#[CoversClass(AbstractSamlElement::class)]
final class AuthenticationStatementTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /** @var string */
    private static string $certificate;

    /** @var string[] */
    private static array $certData;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = AuthenticationStatement::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_AuthenticationStatement.xml',
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
    }


    // marshalling


    /**
     * Test creating an AuthenticationStatement from scratch
     */
    public function testMarshalling(): void
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
            IDValue::fromString('AuthenticationStatementID'),
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

        $authenticationStatement = new AuthenticationStatement(
            $subject,
            SAMLAnyURIValue::fromString(C::AC_PASSWORD),
            SAMLDateTimeValue::fromString('2023-01-24T09:42:26Z'),
            $subjectLocality,
            [$authorityBinding],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($authenticationStatement),
        );
    }


    public function testMarshallingElementOrdering(): void
    {
        $authenticationStatement = AuthenticationStatement::fromXML(self::$xmlRepresentation->documentElement);
        $authenticationStatementElement = $authenticationStatement->toXML();

        // Test for a Subject
        $xpCache = XPath::getXPath($authenticationStatementElement);
        $authenticationStatementElements = XPath::xpQuery(
            $authenticationStatementElement,
            './saml_assertion:Subject',
            $xpCache,
        );
        $this->assertCount(1, $authenticationStatementElements);

        // Test ordering of AuthenticationStatement contents
        /** @var \DOMElement[] $authenticationStatementElements */
        $authenticationStatementElements = XPath::xpQuery(
            $authenticationStatementElement,
            './saml_assertion:Subject/following-sibling::*',
            $xpCache,
        );
        $this->assertCount(2, $authenticationStatementElements);
        $this->assertEquals('saml:SubjectLocality', $authenticationStatementElements[0]->tagName);
        $this->assertEquals('saml:AuthorityBinding', $authenticationStatementElements[1]->tagName);
    }
}
