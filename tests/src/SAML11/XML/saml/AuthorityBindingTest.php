<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Type\SAMLAnyURIValue;
use SimpleSAML\SAML11\XML\saml\AbstractAuthorityBindingType;
use SimpleSAML\SAML11\XML\saml\AbstractSamlElement;
use SimpleSAML\SAML11\XML\saml\AuthorityBinding;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSchema\Type\NCNameValue;
use SimpleSAML\XMLSchema\Type\QNameValue;

use function dirname;
use function strval;

/**
 * Tests for AuthorityBinding elements.
 *
 * @package simplesamlphp/saml11
 */
#[Group('saml')]
#[CoversClass(AuthorityBinding::class)]
#[CoversClass(AbstractAuthorityBindingType::class)]
#[CoversClass(AbstractSamlElement::class)]
final class AuthorityBindingTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$testedClass = AuthorityBinding::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_AuthorityBinding.xml',
        );
    }


    // marshalling


    /**
     * Test creating an AuthorityBinding from scratch
     */
    public function testMarshalling(): void
    {
        $ab = new AuthorityBinding(
            QNameValue::fromParts(
                NCNameValue::fromString('AttributeQuery'),
                SAMLAnyURIValue::fromString(C::NS_SAMLP),
                NCNameValue::fromString('samlp'),
            ),
            SAMLAnyURIValue::fromString('urn:x-simplesamlphp:location'),
            SAMLAnyURIValue::fromString('urn:x-simplesamlphp:binding'),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($ab),
        );
    }
}
