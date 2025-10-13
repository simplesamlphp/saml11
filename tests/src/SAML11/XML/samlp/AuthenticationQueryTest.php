<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\samlp;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Type\SAMLAnyURIValue;
use SimpleSAML\SAML11\Type\SAMLStringValue;
use SimpleSAML\SAML11\XML\saml\ConfirmationMethod;
use SimpleSAML\SAML11\XML\saml\NameIdentifier;
use SimpleSAML\SAML11\XML\saml\Subject;
use SimpleSAML\SAML11\XML\saml\SubjectConfirmation;
use SimpleSAML\SAML11\XML\saml\SubjectConfirmationData;
use SimpleSAML\SAML11\XML\samlp\AbstractAuthenticationQueryType;
use SimpleSAML\SAML11\XML\samlp\AbstractQueryAbstractType;
use SimpleSAML\SAML11\XML\samlp\AbstractSamlpElement;
use SimpleSAML\SAML11\XML\samlp\AbstractSubjectQueryAbstractType;
use SimpleSAML\SAML11\XML\samlp\AuthenticationQuery;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Type\Base64BinaryValue;
use SimpleSAML\XMLSchema\Type\IDValue;
use SimpleSAML\XMLSchema\Type\StringValue;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XML\ds\KeyInfo;
use SimpleSAML\XMLSecurity\XML\ds\KeyName;
use SimpleSAML\XMLSecurity\XML\ds\X509Certificate;
use SimpleSAML\XMLSecurity\XML\ds\X509Data;
use SimpleSAML\XMLSecurity\XML\ds\X509SubjectName;

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
        $authnQuery = new AuthenticationQuery(
            $subject,
            SAMLAnyURIValue::fromString(C::AC_PASSWORD),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($authnQuery),
        );
    }
}
