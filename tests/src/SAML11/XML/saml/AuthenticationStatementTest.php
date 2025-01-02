<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Utils\XPath;
use SimpleSAML\SAML11\XML\saml\AbstractAuthenticationStatementType;
use SimpleSAML\SAML11\XML\saml\AbstractSamlElement;
use SimpleSAML\SAML11\XML\saml\AuthenticationStatement;
use SimpleSAML\SAML11\XML\saml\AuthorityBinding;
use SimpleSAML\SAML11\XML\saml\ConfirmationMethod;
use SimpleSAML\SAML11\XML\saml\NameIdentifier;
use SimpleSAML\SAML11\XML\saml\Subject;
use SimpleSAML\SAML11\XML\saml\SubjectConfirmation;
use SimpleSAML\SAML11\XML\saml\SubjectConfirmationData;
use SimpleSAML\SAML11\XML\saml\SubjectLocality;
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
 * Tests for AuthenticationStatement elements.
 *
 * @package simplesamlphp/saml11
 */
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
        self::$schemaFile = dirname(__FILE__, 6) . '/resources/schemas/oasis-sstc-saml-schema-assertion-1.1.xsd';

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
            'AuthenticationStatementID',
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

        $authenticationStatement = new AuthenticationStatement(
            $subject,
            C::AC_PASSWORD,
            new DateTimeImmutable('2023-01-24T09:42:26Z'),
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
