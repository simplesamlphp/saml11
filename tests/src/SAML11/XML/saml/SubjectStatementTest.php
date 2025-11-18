<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Compat\AbstractContainer;
use SimpleSAML\SAML11\Compat\ContainerSingleton;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Type\SAMLAnyURIValue;
use SimpleSAML\SAML11\Type\SAMLStringValue;
use SimpleSAML\SAML11\XML\saml\AbstractSamlElement;
use SimpleSAML\SAML11\XML\saml\AbstractStatementType;
use SimpleSAML\SAML11\XML\saml\AbstractSubjectStatement;
use SimpleSAML\SAML11\XML\saml\AbstractSubjectStatementType;
use SimpleSAML\SAML11\XML\saml\Audience;
use SimpleSAML\SAML11\XML\saml\ConfirmationMethod;
use SimpleSAML\SAML11\XML\saml\NameIdentifier;
use SimpleSAML\SAML11\XML\saml\Subject;
use SimpleSAML\SAML11\XML\saml\SubjectConfirmation;
use SimpleSAML\SAML11\XML\saml\SubjectConfirmationData;
use SimpleSAML\SAML11\XML\saml\UnknownSubjectStatement;
use SimpleSAML\Test\SAML11\CustomSubjectStatement;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Constants as C_XSI;
use SimpleSAML\XMLSchema\Type\Base64BinaryValue;
use SimpleSAML\XMLSchema\Type\IDValue;
use SimpleSAML\XMLSchema\Type\IntegerValue;
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
 * Tests for SubjectStatement elements.
 *
 * @package simplesamlphp/saml11
 */
#[Group('saml')]
#[CoversClass(AbstractSubjectStatement::class)]
#[CoversClass(AbstractSubjectStatementType::class)]
#[CoversClass(AbstractStatementType::class)]
#[CoversClass(AbstractSamlElement::class)]
final class SubjectStatementTest extends TestCase
{
    use SerializableElementTestTrait;


    /** @var string */
    private static string $certificate;

    /** @var string[] */
    private static array $certData;

    /** @var \SimpleSAML\SAML11\Compat\AbstractContainer */
    private static AbstractContainer $containerBackup;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$containerBackup = ContainerSingleton::getInstance();

        self::$testedClass = CustomSubjectStatement::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_SubjectStatement.xml',
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

        $container = clone self::$containerBackup;
        $container->registerExtensionHandler(CustomSubjectStatement::class);
        ContainerSingleton::setContainer($container);
    }


    /**
     */
    public static function tearDownAfterClass(): void
    {
        ContainerSingleton::setContainer(self::$containerBackup);
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
            IDValue::fromString('SubjectStatementID'),
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

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($subjectStatement),
        );
    }


    /**
     * Test unmarshalling a registered class
     */
    public function testUnmarshalling(): void
    {
        $subjectStatement = CustomSubjectStatement::fromXML(self::$xmlRepresentation->documentElement);

        $this->assertEquals('ssp:CustomSubjectStatementType', $subjectStatement->getXsiType());
        $audience = $subjectStatement->getAudience();
        $this->assertCount(1, $audience);
        $this->assertEquals('urn:x-simplesamlphp:audience', $audience[0]->getContent());

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($subjectStatement),
        );
    }


    /**
     */
    public function testUnmarshallingUnregistered(): void
    {
        $element = clone self::$xmlRepresentation->documentElement;
        $element->setAttributeNS(C_XSI::NS_XSI, 'xsi:type', 'ssp:UnknownSubjectStatementType');

        $subjectStatement = AbstractSubjectStatement::fromXML($element);

        $this->assertInstanceOf(UnknownSubjectStatement::class, $subjectStatement);
        $this->assertEquals(
            '{urn:x-simplesamlphp:namespace}ssp:UnknownSubjectStatementType',
            $subjectStatement->getXsiType()->getRawValue(),
        );

        $chunk = $subjectStatement->getRawSubjectStatement();
        $this->assertEquals('saml', $chunk->getPrefix());
        $this->assertEquals('SubjectStatement', $chunk->getLocalName());
        $this->assertEquals(C::NS_SAML, $chunk->getNamespaceURI());

        $this->assertEquals($element->ownerDocument?->saveXML($element), strval($subjectStatement));
    }
}
