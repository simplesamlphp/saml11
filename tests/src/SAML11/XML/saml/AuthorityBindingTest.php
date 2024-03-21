<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\XML\saml\AbstractAuthorityBindingType;
use SimpleSAML\SAML11\XML\saml\AbstractSamlElement;
use SimpleSAML\SAML11\XML\saml\AuthorityBinding;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;

use function dirname;
use function strval;

/**
 * Tests for AuthorityBinding elements.
 *
 * @package simplesamlphp/saml11
 */
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
        self::$schemaFile = dirname(__FILE__, 6) . '/resources/schemas/oasis-sstc-saml-schema-assertion-1.1.xsd';

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
            'samlp:AttributeQuery',
            'urn:x-simplesamlphp:location',
            'urn:x-simplesamlphp:binding',
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($ab),
        );
    }
}
