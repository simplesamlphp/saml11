<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\SAML11\Type\{AnyURIValue, StringValue};

use function strval;

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
     * @param \SimpleSAML\SAML11\Type\StringValue $AttributeName
     * @param \SimpleSAML\SAML11\Type\AnyURIValue $AttributeNamespace
     */
    public function __construct(
        protected StringValue $AttributeName,
        protected AnyURIValue $AttributeNamespace,
    ) {
    }


    /**
     * Collect the value of the AttributeName-property
     *
     * @return \SimpleSAML\SAML11\Type\StringValue
     */
    public function getAttributeName(): StringValue
    {
        return $this->AttributeName;
    }


    /**
     * Collect the value of the AttributeNamespace-property
     *
     * @return \SimpleSAML\SAML11\Type\AnyURIValue
     */
    public function getAttributeNamespace(): AnyURIValue
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

        $e->setAttribute('AttributeName', strval($this->getAttributeName()));
        $e->setAttribute('AttributeNamespace', strval($this->getAttributeNamespace()));

        return $e;
    }
}
