<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\samlp;

use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Type\SAMLAnyURIValue;
use SimpleSAML\SAML11\XML\samlp\{AbstractSamlpElement, RespondWith};
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Type\{NCNameValue, QNameValue};

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\SAML11\XML\samlp\RespondWithTest
 *
 * @package simplesamlphp/saml11
 */
#[Group('samlp')]
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
        $respondWith = new RespondWith(
            QNameValue::fromParts(
                NCNameValue::fromString('RespondWith'),
                SAMLAnyURIValue::fromString(RespondWith::NS),
                NCNameValue::fromString(RespondWith::NS_PREFIX),
            ),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($respondWith),
        );
    }
}
