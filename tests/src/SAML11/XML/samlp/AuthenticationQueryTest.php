<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\samlp;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Type\{AnyURIValue, StringValue};
use SimpleSAML\SAML11\XML\saml\{
    ConfirmationMethod,
    NameIdentifier,
    Subject,
    SubjectConfirmation,
    SubjectConfirmationData,
};
use SimpleSAML\SAML11\XML\samlp\{
    AbstractAuthenticationQueryType,
    AbstractQueryAbstractType,
    AbstractSamlpElement,
    AbstractSubjectQueryAbstractType,
    AuthenticationQuery,
};
use SimpleSAML\XML\{Chunk, DOMDocumentFactory};
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XML\Type\{Base64BinaryValue, IDValue, StringValue as BaseStringValue};
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
 * Tests for AuthenticationQuery elements.
 *
 * @package simplesamlphp/saml11
 */
#[Group('samlp')]
#[CoversClass(AuthenticationQuery::class)]
#[CoversClass(AbstractAuthenticationQueryType::class)]
#[CoversClass(AbstractSubjectQueryAbstractType::class)]
#[CoversClass(AbstractQueryAbstractType::class)]
#[CoversClass(AbstractSamlpElement::class)]
final class AuthenticationQueryTest extends TestCase
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
        self::$testedClass = AuthenticationQuery::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/samlp_AuthenticationQuery.xml',
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
            StringValue::fromString('phpunit'),
        );

        $keyInfo = new KeyInfo(
            [
                new KeyName(
                    BaseStringValue::fromString('testkey'),
                ),
                new X509Data(
                    [
                        new X509Certificate(
                            Base64BinaryValue::fromString(self::$certificate),
                        ),
                        new X509SubjectName(
                            BaseStringValue::fromString(self::$certData['name']),
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
                    AnyURIValue::fromString('_Test1'),
                ),
                new ConfirmationMethod(
                    AnyURIValue::fromString('_Test2'),
                ),
            ],
            $scd,
            $keyInfo,
        );

        $nameIdentifier = new NameIdentifier(
            StringValue::fromString('TheNameIDValue'),
            StringValue::fromString('TheNameQualifier'),
            AnyURIValue::fromString('urn:the:format'),
        );

        $subject = new Subject($sc, $nameIdentifier);
        $authnQuery = new AuthenticationQuery(
            $subject,
            AnyURIValue::fromString(C::AC_PASSWORD),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($authnQuery),
        );
    }
}
