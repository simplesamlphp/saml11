<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\ExtendableElementTrait;
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSchema\Exception\SchemaViolationException;
use SimpleSAML\XMLSchema\XML\Constants\NS;

/**
 * SAML AdviceType abstract data type.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractAdviceType extends AbstractSamlElement
{
    use ExtendableElementTrait;


    /** The namespace-attribute for the xs:any element */
    public const string XS_ANY_ELT_NAMESPACE = NS::OTHER;


    /**
     * Initialize a saml:AdviceType from scratch
     *
     * @param array<\SimpleSAML\SAML11\XML\saml\AssertionIDReference> $assertionIDReference
     * @param array<\SimpleSAML\SAML11\XML\saml\Assertion> $assertion
     * @param \SimpleSAML\XML\SerializableElementInterface[] $elements
     */
    final public function __construct(
        protected array $assertionIDReference = [],
        protected array $assertion = [],
        array $elements = [],
    ) {
        Assert::maxCount($assertionIDReference, C::UNBOUNDED_LIMIT);
        Assert::maxCount($assertion, C::UNBOUNDED_LIMIT);
        Assert::allIsInstanceOf($assertionIDReference, AssertionIDReference::class, SchemaViolationException::class);
        Assert::allIsInstanceOf($assertion, Assertion::class, SchemaViolationException::class);

        $this->setElements($elements);
    }


    /**
     * Collect the value of the assertionIDReference-property
     *
     * @return array<\SimpleSAML\SAML11\XML\saml\AssertionIDReference>
     */
    public function getAssertionIDReference(): array
    {
        return $this->assertionIDReference;
    }


    /**
     * Collect the value of the assertion-property
     *
     * @return array<\SimpleSAML\SAML11\XML\saml\Assertion>
     */
    public function getAssertion(): array
    {
        return $this->assertion;
    }


    /**
     * Test if an object, at the state it's in, would produce an empty XML-element
     */
    public function isEmptyElement(): bool
    {
        return empty($this->assertionIDReference)
            && empty($this->assertion)
            && empty($this->getElements());
    }


    /**
     * Convert XML into an AdviceType
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, static::getLocalName(), InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, static::NS, InvalidDOMElementException::class);

        $elements = [];
        foreach ($xml->childNodes as $element) {
            if ($element->namespaceURI === C::NS_SAML) {
                continue;
            } elseif (!($element instanceof DOMElement)) {
                continue;
            }

            $elements[] = new Chunk($element);
        }

        return new static(
            AssertionIDReference::getChildrenOfClass($xml),
            Assertion::getChildrenOfClass($xml),
            $elements,
        );
    }


    /**
     * Convert this EvidenceType to XML.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        foreach ($this->getAssertionIDReference() as $assertionIDRef) {
            $assertionIDRef->toXML($e);
        }

        foreach ($this->getAssertion() as $assertion) {
            $assertion->toXML($e);
        }

        foreach ($this->getElements() as $element) {
            $element->toXML($e);
        }

        return $e;
    }
}
