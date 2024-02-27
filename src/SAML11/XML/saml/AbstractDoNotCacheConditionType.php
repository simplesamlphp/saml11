<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\XML\saml\AbstractConditionType;
use SimpleSAML\XML\Exception\InvalidDOMElementException;

/**
 * @package simplesamlphp\saml11
 */
abstract class AbstractDoNotCacheConditionType extends AbstractConditionType
{
    /**
     * DoNotCacheConditionType constructor.
     */
    public function __construct()
    {
    }


    /**
     * Convert XML into a DoNotCacheCondition
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
        Assert::same($xml->namespaceURI, static::getNamespaceURI(), InvalidDOMElementException::class);

        return new static();
    }


    /**
     * Convert this DoNotCacheCondition to XML.
     *
     * @param \DOMElement $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this DoNotCacheCondition.
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        return $this->instantiateParentElement($parent);
    }
}
