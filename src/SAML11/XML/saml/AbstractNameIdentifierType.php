<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Type\{AnyURIValue, StringValue};
use SimpleSAML\XML\Exception\InvalidDOMElementException;

use function strval;

/**
 * SAML NameIdentifierType abstract data type.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractNameIdentifierType extends AbstractSamlElement
{
    /**
     * Initialize a saml:NameIdentifierType from scratch
     *
     * @param \SimpleSAML\SAML11\Type\StringValue $value
     * @param \SimpleSAML\SAML11\Type\StringValue|null $NameQualifier
     * @param \SimpleSAML\SAML11\Type\AnyURIValue|null $Format
     */
    final public function __construct(
        protected StringValue $value,
        protected ?StringValue $NameQualifier = null,
        protected ?AnyURIValue $Format = null,
    ) {
    }


    /**
     * Collect the value of the value-property
     *
     * @return \SimpleSAML\SAML11\Type\StringValue
     */
    public function getValue(): StringValue
    {
        return $this->value;
    }


    /**
     * Collect the value of the Format-property
     *
     * @return \SimpleSAML\SAML11\Type\AnyURIValue|null
     */
    public function getFormat(): ?AnyURIValue
    {
        return $this->Format;
    }


    /**
     * Collect the value of the NameQualifier-property
     *
     * @return \SimpleSAML\SAML11\Type\StringValue|null
     */
    public function getNameQualifier(): ?StringValue
    {
        return $this->NameQualifier;
    }


    /**
     * Convert XML into an NameIdentifier
     *
     * @param \DOMElement $xml The XML element we should load
     * @return static
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, static::getLocalName(), InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, static::NS, InvalidDOMElementException::class);

        return new static(
            StringValue::fromString($xml->textContent),
            self::getOptionalAttribute($xml, 'NameQualifier', StringValue::class, null),
            self::getOptionalAttribute($xml, 'Format', AnyURIValue::class, null),
        );
    }


    /**
     * Convert this NameIdentifierType to XML.
     *
     * @param \DOMElement $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this NameIdentifierType.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->textContent = strval($this->getValue());

        if ($this->getNameQualifier() !== null) {
            $e->setAttribute('NameQualifier', strval($this->getNameQualifier()));
        }

        if ($this->getFormat() !== null) {
            $e->setAttribute('Format', strval($this->getFormat()));
        }

        return $e;
    }
}
