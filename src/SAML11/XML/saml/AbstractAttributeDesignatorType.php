<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\SchemaViolationException;

/**
 * SAML AttributeDesignatorType abstract data type.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractAttributeDesignatorType extends AbstractSamlElement
{
    /**
     * Initialize a saml:AttributeDesignatorType from scratch
     *
     * @param string $AttributeName
     * @param string $AttributeNamespace
     */
    public function __construct(
        protected string $AttributeName,
        protected string $AttributeNamespace,
    ) {
        Assert::nullOrNotWhitespaceOnly($AttributeName, SchemaViolationException::class);
        Assert::nullOrValidURI($AttributeNamespace, SchemaViolationException::class); // Covers the empty string
    }


    /**
     * Collect the value of the AttributeName-property
     *
     * @return string
     */
    public function getAttributeName(): string
    {
        return $this->AttributeName;
    }


    /**
     * Collect the value of the AttributeNamespace-property
     *
     * @return string
     */
    public function getAttributeNamespace(): string
    {
        return $this->AttributeNamespace;
    }


    /**
     * Convert this AttributeDesignatorType to XML.
     *
     * @param \DOMElement $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this AttributeDesignatorType.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        $e->setAttribute('AttributeName', $this->getAttributeName());
        $e->setAttribute('AttributeNamespace', $this->getAttributeNamespace());

        return $e;
    }
}
