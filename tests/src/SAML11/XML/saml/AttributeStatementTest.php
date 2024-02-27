<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Utils\XPath;
use SimpleSAML\SAML11\XML\saml\Attribute;
use SimpleSAML\SAML11\XML\saml\AttributeStatement;
use SimpleSAML\SAML11\XML\saml\AttributeValue;
use SimpleSAML\SAML11\XML\saml\ConfirmationMethod;
use SimpleSAML\SAML11\XML\saml\NameIdentifier;
use SimpleSAML\SAML11\XML\saml\Subject;
use SimpleSAML\SAML11\XML\saml\SubjectConfirmation;
use SimpleSAML\SAML11\XML\saml\SubjectConfirmationData;
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
 * Tests for AttributeStatement elements.
 *
 * @covers \SimpleSAML\SAML11\XML\saml\AttributeStatement
 * @covers \SimpleSAML\SAML11\XML\saml\AbstractAttributeStatementType
 * @covers \SimpleSAML\SAML11\XML\saml\AbstractSamlElement
 *
 * @package simplesamlphp/saml11
 */
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
        self::$schemaFile = dirname(__FILE__, 6) . '/resources/schemas/oasis-sstc-saml-schema-assertion-1.1.xsd';

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
                ''
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
                    '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">some</ssp:Chunk>'
                )->documentElement),
            ],
            'fed654',
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
            [new AttributeValue('FirstValue'), new AttributeValue('SecondValue')]
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
        /** @psalm-var \DOMElement[] $authnStatementElements */
        $attributeStatementElements = XPath::xpQuery(
            $attributeStatementElement,
            './saml_assertion:Subject/following-sibling::*',
            $xpCache,
        );
        $this->assertCount(1, $attributeStatementElements);
        $this->assertEquals('saml:Attribute', $attributeStatementElements[0]->tagName);
    }
}
