<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Compat\{AbstractContainer, ContainerSingleton};
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Type\AnyURIValue;
use SimpleSAML\SAML11\XML\saml\{
    AbstractSamlElement,
    AbstractStatement,
    AbstractStatementType,
    Audience,
    UnknownStatement,
};
use SimpleSAML\Test\SAML11\CustomStatement;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\SAML11\XML\saml\StatementTest
 *
 * @package simplesamlphp/saml11
 */
#[Group('saml')]
#[CoversClass(UnknownStatement::class)]
#[CoversClass(AbstractStatement::class)]
#[CoversClass(AbstractStatementType::class)]
#[CoversClass(AbstractSamlElement::class)]
final class StatementTest extends TestCase
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

        self::$testedClass = CustomStatement::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Statement.xml',
        );

        $container = clone self::$containerBackup;
        $container->registerExtensionHandler(CustomStatement::class);
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
        $statement = new CustomStatement(
            [
                new Audience(
                    AnyURIValue::fromString('urn:some:audience'),
                ),
            ],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($statement),
        );
    }


    // unmarshalling


    /**
     * Test unmarshalling a registered class
     */
    public function testUnmarshalling(): void
    {
        $statement = CustomStatement::fromXML(self::$xmlRepresentation->documentElement);
        $this->assertInstanceOf(CustomStatement::class, $statement);

        $this->assertEquals('ssp:CustomStatementType', $statement->getXsiType());
        $audience = $statement->getAudience();
        $this->assertCount(1, $audience);
        $this->assertEquals('urn:some:audience', $audience[0]->getContent());

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($statement),
        );
    }


    /**
     */
    public function testUnmarshallingUnregistered(): void
    {
        $element = clone self::$xmlRepresentation->documentElement;
        $element->setAttributeNS(C::NS_XSI, 'xsi:type', 'ssp:UnknownStatementType');

        $statement = AbstractStatement::fromXML($element);

        $this->assertInstanceOf(UnknownStatement::class, $statement);
        $this->assertEquals(
            '{urn:x-simplesamlphp:namespace}ssp:UnknownStatementType',
            $statement->getXsiType()->getRawValue(),
        );

        $chunk = $statement->getRawStatement();
        $this->assertEquals('saml', $chunk->getPrefix());
        $this->assertEquals('Statement', $chunk->getLocalName());
        $this->assertEquals(C::NS_SAML, $chunk->getNamespaceURI());

        $this->assertEquals($element->ownerDocument?->saveXML($element), strval($statement));
    }
}
