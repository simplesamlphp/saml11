<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\samlp;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\XML\samlp\AbstractSamlpElement;
use SimpleSAML\SAML11\XML\samlp\RespondWith;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\SAML11\XML\samlp\RespondWithTest
 *
 * @package simplesamlphp/saml11
 */
#[CoversClass(RespondWith::class)]
#[CoversClass(AbstractSamlpElement::class)]
final class RespondWithTest extends TestCase
{
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = RespondWith::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/samlp_RespondWith.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $respondWith = new RespondWith(RespondWith::NS_PREFIX . ':RespondWith', RespondWith::NS);

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($respondWith),
        );
    }
}
