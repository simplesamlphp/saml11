<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Type\{
    AnyURIValue as SAMLAnyURIValue,
    StringValue as SAMLStringValue,
};
use SimpleSAML\SAML11\XML\saml\{
    AbstractSamlElement,
    AbstractSubjectType,
    ConfirmationMethod,
    NameIdentifier,
    Subject,
    SubjectConfirmation,
    SubjectConfirmationData,
};
use SimpleSAML\XML\{Chunk, DOMDocumentFactory};
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XML\Type\{Base64BinaryValue, IDValue, IntegerValue, StringValue};
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
 * Tests for Subject elements.
 *
 * @package simplesamlphp/saml11
 */
#[Group('saml')]
#[CoversClass(Subject::class)]
#[CoversClass(AbstractSubjectType::class)]
#[CoversClass(AbstractSamlElement::class)]
final class SubjectTest extends TestCase
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
        self::$testedClass = Subject::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Subject.xml',
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
     * Test creating an Subject from scratch.
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

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($subject),
        );
    }
}
