<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Type\{AnyURIValue, StringValue};
use SimpleSAML\XML\Exception\{InvalidDOMElementException, SchemaViolationException};

/**
 * SAML AttributeType abstract data type.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractAttributeType extends AbstractAttributeDesignatorType
{
    /**
     * Initialize a saml:AttributeType from scratch
     *
     * @param \SimpleSAML\SAML11\Type\StringValue $AttributeName
     * @param \SimpleSAML\SAML11\Type\AnyURIValue $AttributeNamespace
     * @param array<\SimpleSAML\SAML11\XML\saml\AttributeValue> $attributeValue
     */
    final public function __construct(
        StringValue $AttributeName,
        AnyURIValue $AttributeNamespace,
        protected array $attributeValue,
    ) {
        Assert::allIsInstanceOf($attributeValue, AttributeValue::class, SchemaViolationException::class);

        parent::__construct($AttributeName, $AttributeNamespace);
    }


    /**
     * Collect the value of the attributeValue-property
     *
     * @return array<\SimpleSAML\SAML11\XML\saml\AttributeValue>
     */
    public function getAttributeValue(): array
    {
        return $this->attributeValue;
    }


    /**
     * Convert XML into an AttributeType
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

        $attributeValue = AttributeValue::getChildrenOfClass($xml);
        $AttributeName = self::getAttribute($xml, 'AttributeName', StringValue::class);
        $AttributeNamespace = self::getAttribute($xml, 'AttributeNamespace', AnyURIValue::class);

        return new static($AttributeName, $AttributeNamespace, $attributeValue);
    }


    /**
     * Convert this AttributeType to XML.
     *
     * @param \DOMElement $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this AttributeType.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = parent::toXML($parent);

        foreach ($this->getAttributeValue() as $av) {
            $av->toXML($e);
        }

        return $e;
    }
}
