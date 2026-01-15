<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Type\SAMLAnyURIValue;
use SimpleSAML\SAML11\Type\SAMLStringValue;
use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;

/**
 * Class representing a saml:AttributeDesignator element.
 *
 * @package simplesamlphp/saml11
 */
final class AttributeDesignator extends AbstractAttributeDesignatorType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;


    /**
     * Convert XML into an AttributeDesignatorType
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, static::getLocalName(), InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, static::NS, InvalidDOMElementException::class);

        return new static(
            self::getAttribute($xml, 'AttributeName', SAMLStringValue::class),
            self::getAttribute($xml, 'AttributeNamespace', SAMLAnyURIValue::class),
        );
    }
}
