<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\samlp;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Type\{SAMLAnyURIValue, SAMLStringValue};
use SimpleSAML\SAML11\XML\saml\{
    AttributeDesignator,
    ConfirmationMethod,
    NameIdentifier,
    Subject,
    SubjectConfirmation,
    SubjectConfirmationData,
};
use SimpleSAML\SAML11\XML\samlp\{
    AbstractAttributeQueryType,
    AbstractQueryAbstractType,
    AbstractSamlpElement,
    AbstractSubjectQueryAbstractType,
    AttributeQuery,
};
use SimpleSAML\XML\{Chunk, DOMDocumentFactory};
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XMLSchema\Type\{Base64BinaryValue, IDValue, StringValue};
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
 * Tests for AttributeQuery elements.
 *
 * @package simplesamlphp/saml11
 */
#[Group('samlp')]
#[CoversClass(AttributeQuery::class)]
#[CoversClass(AbstractAttributeQueryType::class)]
#[CoversClass(AbstractSubjectQueryAbstractType::class)]
#[CoversClass(AbstractQueryAbstractType::class)]
#[CoversClass(AbstractSamlpElement::class)]
final class AttributeQueryTest extends TestCase
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
        self::$testedClass = AttributeQuery::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/samlp_AttributeQuery.xml',
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
     * Test creating an AuthenticationQuery from scratch.
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
        $attributeQuery = new AttributeQuery(
            $subject,
            SAMLAnyURIValue::fromString('urn:some:resource'),
            [
                new AttributeDesignator(
                    SAMLStringValue::fromString('TheName'),
                    SAMLAnyURIValue::fromString('https://example.org/'),
                ),
                new AttributeDesignator(
                    SAMLStringValue::fromString('TheOtherName'),
                    SAMLAnyURIValue::fromString('https://example.org/'),
                ),
            ],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($attributeQuery),
        );
    }
}
