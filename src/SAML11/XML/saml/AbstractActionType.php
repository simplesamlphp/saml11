<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Type\{SAMLAnyURIValue, SAMLStringValue};
use SimpleSAML\XML\Exception\{InvalidDOMElementException, SchemaViolationException};

use function strval;

/**
 * SAML ActionType abstract data type.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractActionType extends AbstractSamlElement
{
    /**
     * Initialize a saml:AbstractActionType from scratch
     *
     * @param \SimpleSAML\SAML11\Type\SAMLStringValue $value
     * @param \SimpleSAML\SAML11\Type\SAMLAnyURIValue|null $Namespace
     */
    final public function __construct(
        protected SAMLStringValue $value,
        protected ?SAMLAnyURIValue $Namespace = null,
    ) {
    }


    /**
     * Collect the value of the element
     *
     * @return \SimpleSAML\SAML11\Type\SAMLStringValue|null
     */
    public function getValue(): SAMLStringValue
    {
        return $this->value;
    }


    /**
     * Collect the value of the Namespace-property
     *
     * @return \SimpleSAML\SAML11\Type\SAMLAnyURIValue|null
     */
    public function getNamespace(): ?SAMLAnyURIValue
    {
        return $this->Namespace;
    }


    /**
     * Convert XML into an ActionType
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
            self::getOptionalAttribute($xml, 'Namespace', SAMLAnyURIValue::class),
        );
    }


    /**
     * Convert this ActionType to XML.
     *
     * @param \DOMElement $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this ActionType.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->textContent = strval($this->getValue());

        if ($this->getNamespace() !== null) {
            $e->setAttribute('Namespace', strval($this->getNamespace()));
        }

        return $e;
    }
}
