<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;

/**
 * Class representing a saml:DoNotCacheConditionType.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractDoNotCacheConditionType extends AbstractConditionType
{
    /**
     * DoNotCacheConditionType constructor.
     */
    final public function __construct()
    {
    }


    /**
     * Convert XML into a DoNotCacheCondition
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
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        return $this->instantiateParentElement($parent);
    }
}
