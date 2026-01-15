<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Type\SAMLStringValue;
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;

use function strval;

/**
 * SAML SubjectLocalityType abstract data type.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractSubjectLocalityType extends AbstractSamlElement
{
    /**
     * Initialize a saml:SubjectLocalityType from scratch
     *
     * @param \SimpleSAML\SAML11\Type\SAMLStringValue|null $IPAddress
     * @param \SimpleSAML\SAML11\Type\SAMLStringValue|null $DNSAddress
     */
    final public function __construct(
        protected ?SAMLStringValue $IPAddress = null,
        protected ?SAMLStringValue $DNSAddress = null,
    ) {
    }


    /**
     * Collect the value of the IPAddress-property
     *
     * @return \SimpleSAML\SAML11\Type\SAMLStringValue|null
     */
    public function getIPAddress(): ?SAMLStringValue
    {
        return $this->IPAddress;
    }


    /**
     * Collect the value of the DNSAddress-property
     *
     * @return \SimpleSAML\SAML11\Type\SAMLStringValue|null
     */
    public function getDNSAddress(): ?SAMLStringValue
    {
        return $this->DNSAddress;
    }


    /**
     * Test if an object, at the state it's in, would produce an empty XML-element
     */
    public function isEmptyElement(): bool
    {
        return empty($this->getIPAddress())
            && empty($this->getDNSAddress());
    }


    /**
     * Convert XML into an SubjectLocalityType
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, static::getLocalName(), InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, static::NS, InvalidDOMElementException::class);

        $IPAddress = self::getOptionalAttribute($xml, 'IPAddress', SAMLStringValue::class);
        $DNSAddress = self::getOptionalAttribute($xml, 'DNSAddress', SAMLStringValue::class);

        return new static($IPAddress, $DNSAddress);
    }


    /**
     * Convert this SubjectLocalityType to XML.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        if ($this->getIPAddress() !== null) {
            $e->setAttribute('IPAddress', strval($this->getIPAddress()));
        }

        if ($this->getDNSAddress() !== null) {
            $e->setAttribute('DNSAddress', strval($this->getDNSAddress()));
        }

        return $e;
    }
}
