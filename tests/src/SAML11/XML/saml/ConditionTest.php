<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Compat\AbstractContainer;
use SimpleSAML\SAML11\Compat\ContainerSingleton;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\XML\saml\AbstractCondition;
use SimpleSAML\SAML11\XML\saml\Audience;
use SimpleSAML\SAML11\XML\saml\UnknownCondition;
use SimpleSAML\Test\SAML11\CustomCondition;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\SAML11\XML\saml\ConditionTest
 *
 * @covers \SimpleSAML\SAML11\XML\saml\UnknownCondition
 * @covers \SimpleSAML\SAML11\XML\saml\AbstractCondition
 * @covers \SimpleSAML\SAML11\XML\saml\AbstractConditionType
 * @covers \SimpleSAML\SAML11\XML\saml\AbstractSamlElement
 *
 * @package simplesamlphp/saml11
 */
final class ConditionTest extends TestCase
{
    use SerializableElementTestTrait;


    /** @var \SimpleSAML\SAML11\Compat\AbstractContainer */
    private static AbstractContainer $containerBackup;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$containerBackup = ContainerSingleton::getInstance();

        self::$testedClass = CustomCondition::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Condition.xml',
        );

        $container = clone self::$containerBackup;
        $container->registerExtensionHandler(CustomCondition::class);
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
        $condition = new CustomCondition(
            [new Audience('urn:some:audience')],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($condition),
        );
    }


    // unmarshalling


    /**
     */
    public function testUnmarshallingUnregistered(): void
    {
        $element = clone self::$xmlRepresentation->documentElement;
        $element->setAttributeNS(C::NS_XSI, 'xsi:type', 'ssp:UnknownConditionType');

        $condition = AbstractCondition::fromXML($element);

        $this->assertInstanceOf(UnknownCondition::class, $condition);
        $this->assertEquals('urn:x-simplesamlphp:namespace:UnknownConditionType', $condition->getXsiType());

        $chunk = $condition->getRawCondition();
        $this->assertEquals('saml', $chunk->getPrefix());
        $this->assertEquals('Condition', $chunk->getLocalName());
        $this->assertEquals(C::NS_SAML, $chunk->getNamespaceURI());

        $this->assertEquals($element->ownerDocument?->saveXML($element), strval($condition));
    }
}