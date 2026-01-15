<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\SAML11\Type\SAMLAnyURIValue;
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSchema\Type\QNameValue;

use function strval;

/**
 * SAML AuthorityBindingType abstract data type.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractAuthorityBindingType extends AbstractSamlElement
{
    /**
     * Initialize a saml:AuthorityBindingType from scratch
     *
     * @param \SimpleSAML\XMLSchema\Type\QNameValue $AuthorityKind
     * @param \SimpleSAML\SAML11\Type\SAMLAnyURIValue $Location
     * @param \SimpleSAML\SAML11\Type\SAMLAnyURIValue $Binding
     */
    final public function __construct(
        protected QNameValue $AuthorityKind,
        protected SAMLAnyURIValue $Location,
        protected SAMLAnyURIValue $Binding,
    ) {
    }


    /**
     * Collect the value of the AuthorityKind-property
     *
     * @return \SimpleSAML\XMLSchema\Type\QNameValue
     */
    public function getAuthorityKind(): QNameValue
    {
        return $this->AuthorityKind;
    }


    /**
     * Collect the value of the Location-property
     *
     * @return \SimpleSAML\SAML11\Type\SAMLAnyURIValue
     */
    public function getLocation(): SAMLAnyURIValue
    {
        return $this->Location;
    }


    /**
     * Collect the value of the Binding-property
     *
     * @return \SimpleSAML\SAML11\Type\SAMLAnyURIValue
     */
    public function getBinding(): SAMLAnyURIValue
    {
        return $this->Binding;
    }


    /**
     * Convert XML into an AuthorityBindingType
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, static::getLocalName(), InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, static::NS, InvalidDOMElementException::class);

        $AuthorityKind = self::getAttribute($xml, 'AuthorityKind', QNameValue::class);
        $Location = self::getAttribute($xml, 'Location', SAMLAnyURIValue::class);
        $Binding = self::getAttribute($xml, 'Binding', SAMLAnyURIValue::class);

        return new static($AuthorityKind, $Location, $Binding);
    }


    /**
     * Convert this AuthorityBindingType to XML.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        if (!$e->lookupPrefix($this->getAuthorityKind()->getNamespaceURI()->getValue())) {
            $e->setAttributeNS(
                'http://www.w3.org/2000/xmlns/',
                'xmlns:' . $this->getAuthorityKind()->getNamespacePrefix()->getValue(),
                $this->getAuthorityKind()->getNamespaceURI()->getValue(),
            );
        }

        $e->setAttribute('AuthorityKind', strval($this->getAuthorityKind()));
        $e->setAttribute('Location', strval($this->getLocation()));
        $e->setAttribute('Binding', strval($this->getBinding()));

        return $e;
    }
}
