<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Type\{SAMLAnyURIValue, SAMLStringValue};
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;

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
     * @param \SimpleSAML\SAML11\Type\SAMLStringValue $value
     * @param \SimpleSAML\SAML11\Type\SAMLStringValue|null $NameQualifier
     * @param \SimpleSAML\SAML11\Type\SAMLAnyURIValue|null $Format
     */
    final public function __construct(
        protected SAMLStringValue $value,
        protected ?SAMLStringValue $NameQualifier = null,
        protected ?SAMLAnyURIValue $Format = null,
    ) {
    }


    /**
     * Collect the value of the value-property
     *
     * @return \SimpleSAML\SAML11\Type\SAMLStringValue
     */
    public function getValue(): SAMLStringValue
    {
        return $this->value;
    }


    /**
     * Collect the value of the Format-property
     *
     * @return \SimpleSAML\SAML11\Type\SAMLAnyURIValue|null
     */
    public function getFormat(): ?SAMLAnyURIValue
    {
        return $this->Format;
    }


    /**
     * Collect the value of the NameQualifier-property
     *
     * @return \SimpleSAML\SAML11\Type\SAMLStringValue|null
     */
    public function getNameQualifier(): ?SAMLStringValue
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
            SAMLStringValue::fromString($xml->textContent),
            self::getOptionalAttribute($xml, 'NameQualifier', SAMLStringValue::class, null),
            self::getOptionalAttribute($xml, 'Format', SAMLAnyURIValue::class, null),
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
