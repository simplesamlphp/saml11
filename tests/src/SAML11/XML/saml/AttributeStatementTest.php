<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Type\{AnyURIValue, StringValue};
use SimpleSAML\SAML11\Utils\XPath;
use SimpleSAML\SAML11\XML\saml\{
    AbstractAttributeStatementType,
    AbstractSamlElement,
    Attribute,
    AttributeStatement,
    AttributeValue,
    ConfirmationMethod,
    NameIdentifier,
    Subject,
    SubjectConfirmation,
    SubjectConfirmationData,
};
use SimpleSAML\XML\{Chunk, DOMDocumentFactory};
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XML\Type\{Base64BinaryValue, IDValue, IntegerValue, StringValue as BaseStringValue};
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
 * Tests for AttributeStatement elements.
 *
 * @package simplesamlphp/saml11
 */
#[Group('saml')]
#[CoversClass(AttributeStatement::class)]
#[CoversClass(AbstractAttributeStatementType::class)]
#[CoversClass(AbstractSamlElement::class)]
final class AttributeStatementTest extends TestCase
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
        self::$testedClass = AttributeStatement::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_AttributeStatement.xml',
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
     * Test creating an AttributeStatement from scratch
     */
    public function testMarshalling(): void
    {
        $scd = new SubjectConfirmationData(
            IntegerValue::fromString('2'),
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
            IDValue::fromString('AttributeStatementID'),
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

        $attribute = new Attribute(
            StringValue::fromString('TheName'),
            AnyURIValue::fromString('https://example.org/'),
            [
                new AttributeValue(
                    StringValue::fromString('FirstValue'),
                ),
                new AttributeValue(
                    StringValue::fromString('SecondValue'),
                ),
            ],
        );

        $attributeStatement = new AttributeStatement(
            $subject,
            [$attribute],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($attributeStatement),
        );
    }


    public function testMarshallingElementOrdering(): void
    {
        $attributeStatement = AttributeStatement::fromXML(self::$xmlRepresentation->documentElement);
        $attributeStatementElement = $attributeStatement->toXML();

        // Test for a Subject
        $xpCache = XPath::getXPath($attributeStatementElement);
        $attributeStatementElements = XPath::xpQuery(
            $attributeStatementElement,
            './saml_assertion:Subject',
            $xpCache,
        );
        $this->assertCount(1, $attributeStatementElements);

        // Test ordering of AttributeStatement contents
        /** @var \DOMElement[] $attributeStatementElements */
        $attributeStatementElements = XPath::xpQuery(
            $attributeStatementElement,
            './saml_assertion:Subject/following-sibling::*',
            $xpCache,
        );
        $this->assertCount(1, $attributeStatementElements);
        $this->assertEquals('saml:Attribute', $attributeStatementElements[0]->tagName);
    }
}
