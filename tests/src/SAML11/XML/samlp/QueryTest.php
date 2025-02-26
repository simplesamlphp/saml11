<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\samlp;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Compat\{AbstractContainer, ContainerSingleton};
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Type\SAMLStringValue;
use SimpleSAML\SAML11\XML\samlp\{
    AbstractQuery,
    AbstractQueryAbstractType,
    AbstractSamlpElement,
    StatusMessage,
    UnknownQuery,
};
use SimpleSAML\Test\SAML11\CustomQuery;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\SAML11\XML\samlp\QueryTest
 *
 * @package simplesamlphp/saml11
 */
#[Group('samlp')]
#[CoversClass(UnknownQuery::class)]
#[CoversClass(AbstractQuery::class)]
#[CoversClass(AbstractQueryAbstractType::class)]
#[CoversClass(AbstractSamlpElement::class)]
final class QueryTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /** @var \SimpleSAML\SAML11\Compat\AbstractContainer */
    private static AbstractContainer $containerBackup;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$containerBackup = ContainerSingleton::getInstance();

        self::$schemaFile = dirname(__FILE__, 5) . '/resources/schemas/simplesamlphp.xsd';

        self::$testedClass = AbstractQuery::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/samlp_Query.xml',
        );

        $container = clone self::$containerBackup;
        $container->registerExtensionHandler(CustomQuery::class);
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
     */
    public function testMarshalling(): void
    {
        $query = new CustomQuery(
            [
                new StatusMessage(
                    SAMLStringValue::fromString('urn:some:audience'),
                ),
            ],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($query),
        );
    }


    // unmarshalling


    /**
     */
    public function testUnmarshallingUnregistered(): void
    {
        $element = clone self::$xmlRepresentation->documentElement;
        $element->setAttributeNS(C::NS_XSI, 'xsi:type', 'ssp:UnknownQueryType');

        $query = AbstractQuery::fromXML($element);

        $this->assertInstanceOf(UnknownQuery::class, $query);
        $this->assertEquals(
            '{urn:x-simplesamlphp:namespace}ssp:UnknownQueryType',
            $query->getXsiType()->getRawValue(),
        );

        $chunk = $query->getRawQuery();
        $this->assertEquals('samlp', $chunk->getPrefix());
        $this->assertEquals('Query', $chunk->getLocalName());
        $this->assertEquals(C::NS_SAMLP, $chunk->getNamespaceURI());

        $this->assertEquals($element->ownerDocument?->saveXML($element), strval($query));
    }
}
