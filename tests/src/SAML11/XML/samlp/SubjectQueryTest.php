<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\samlp;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Compat\{AbstractContainer, ContainerSingleton};
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
    AbstractSamlpElement,
    AbstractSubjectQuery,
    AbstractSubjectQueryAbstractType,
    StatusMessage,
    UnknownSubjectQuery,
};
use SimpleSAML\Test\SAML11\CustomSubjectQuery;
use SimpleSAML\XML\{Chunk, DOMDocumentFactory};
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};
use SimpleSAML\XML\Type\{Base64BinaryValue, IDValue};
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
 * Tests for SubjectQuery elements.
 *
 * @package simplesamlphp/saml11
 */
#[Group('samlp')]
#[CoversClass(AbstractSubjectQuery::class)]
#[CoversClass(AbstractSubjectQueryAbstractType::class)]
#[CoversClass(AbstractSamlpElement::class)]
final class SubjectQueryTest extends TestCase
{
    use SchemaValidationTestTrait;
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

        self::$schemaFile = dirname(__FILE__, 5) . '/resources/schemas/simplesamlphp.xsd';

        self::$testedClass = AbstractSubjectQuery::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/samlp_SubjectQuery.xml',
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
        $container->registerExtensionHandler(CustomSubjectQuery::class);
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
     * Test creating an SubjectQuery from scratch.
     */
    public function testMarshalling(): void
    {
        $scd = new SubjectConfirmationData(
            StringValue::fromString('phpunit'),
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
        $subjectQuery = new CustomSubjectQuery(
            $subject,
            [
                new StatusMessage(
                    StringValue::fromString('urn:some:audience'),
                ),
            ],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($subjectQuery),
        );
    }


    // unmarshalling


    /**
     */
    public function testUnmarshallingUnregistered(): void
    {
        $element = clone self::$xmlRepresentation->documentElement;
        $element->setAttributeNS(C::NS_XSI, 'xsi:type', 'ssp:UnknownSubjectQueryType');
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:ssp', 'urn:x-simplesamlphp:namespace');

        // Normalize the DOMElement by importing it into a clean empty document
        $newDoc = DOMDocumentFactory::create();
        /** @var \DOMElement $element */
        $element = $newDoc->appendChild($newDoc->importNode($element, true));

        $subjectQuery = AbstractSubjectQuery::fromXML($element);

        $this->assertInstanceOf(UnknownSubjectQuery::class, $subjectQuery);
        $this->assertEquals(
            '{urn:x-simplesamlphp:namespace}ssp:UnknownSubjectQueryType',
            $subjectQuery->getXsiType()->getRawValue(),
        );

        $chunk = $subjectQuery->getRawSubjectQuery();
        $this->assertEquals('samlp', $chunk->getPrefix());
        $this->assertEquals('SubjectQuery', $chunk->getLocalName());
        $this->assertEquals(C::NS_SAMLP, $chunk->getNamespaceURI());

//        $this->assertEquals($newDoc->saveXML($element), strval($subjectQuery));
    }
}
