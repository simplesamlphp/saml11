<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Type\{AnyURIValue, StringValue};
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
     * @param \SimpleSAML\SAML11\Type\StringValue $value
     * @param \SimpleSAML\SAML11\Type\AnyURIValue|null $Namespace
     */
    final public function __construct(
        protected StringValue $value,
        protected ?AnyURIValue $Namespace = null,
    ) {
    }


    /**
     * Collect the value of the element
     *
     * @return \SimpleSAML\SAML11\XML\StringValue|null
     */
    public function getValue(): StringValue
    {
        return $this->value;
    }


    /**
     * Collect the value of the Namespace-property
     *
     * @return \SimpleSAML\SAML11\XML\AnyURIValue|null
     */
    public function getNamespace(): ?AnyURIValue
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
            StringValue::fromString($xml->textContent),
            self::getOptionalAttribute($xml, 'Namespace', AnyURIValue::class),
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
