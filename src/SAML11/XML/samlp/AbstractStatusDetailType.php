<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\ExtendableElementTrait;
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSchema\XML\Constants\NS;

/**
 * SAML StatusDetail data type.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractStatusDetailType extends AbstractSamlpElement
{
    use ExtendableElementTrait;


    /** The namespace-attribute for the xs:any element */
    public const string XS_ANY_ELT_NAMESPACE = NS::ANY;


    /**
     * Initialize a samlp:StatusDetail
     *
     * @param \SimpleSAML\XML\Chunk[] $details
     */
    final public function __construct(array $details = [])
    {
        $this->setElements($details);
    }


    /**
     * Test if an object, at the state it's in, would produce an empty XML-element
     */
    public function isEmptyElement(): bool
    {
        return empty($this->elements);
    }


    /**
     * Convert XML into a StatusDetail
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'StatusDetail', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, StatusDetail::NS, InvalidDOMElementException::class);

        $details = [];
        foreach ($xml->childNodes as $detail) {
            if (!($detail instanceof DOMElement)) {
                continue;
            }

            $details[] = new Chunk($detail);
        }

        return new static($details);
    }


    /**
     * Convert this StatusDetail to XML.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        foreach ($this->getElements() as $detail) {
            $detail->toXML($e);
        }

        return $e;
    }
}
