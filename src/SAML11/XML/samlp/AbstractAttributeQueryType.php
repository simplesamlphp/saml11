<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\XML\saml\AttributeDesignator;
use SimpleSAML\SAML11\XML\saml\Subject;
use SimpleSAML\XMLSchema\Exception\SchemaViolationException;
use SimpleSAML\XMLSchema\Type\AnyURIValue;

use function strval;

/**
 * Abstract class to be implemented by all the attributes queries in this namespace
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractAttributeQueryType extends AbstractSubjectQueryAbstractType
{
    /**
     * Initialize a samlp:AttributeQuery element.
     *
     * @param \SimpleSAML\SAML11\XML\saml\Subject $subject
     * @param \SimpleSAML\XMLSchema\Type\AnyURIValue|null $resource
     * @param array<\SimpleSAML\SAML11\XML\saml\AttributeDesignator> $attributeDesignator
     */
    public function __construct(
        Subject $subject,
        protected ?AnyURIValue $resource = null,
        protected array $attributeDesignator = [],
    ) {
        Assert::allIsInstanceOf($attributeDesignator, AttributeDesignator::class, SchemaViolationException::class);

        parent::__construct($subject);
    }


    /**
     * @return \SimpleSAML\XMLSchema\Type\AnyURIValue|null
     */
    public function getResource(): ?AnyURIValue
    {
        return $this->resource;
    }


    /**
     * @return array<\SimpleSAML\SAML11\XML\saml\AttributeDesignator>
     */
    public function getAttributeDesignator(): array
    {
        return $this->attributeDesignator;
    }


    /**
     * Convert this AttributeQuery to XML.
     *
     * @param \DOMElement $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this AttributeQuery.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = parent::toXML($parent);

        if ($this->getResource() !== null) {
            $e->setAttribute('Resource', strval($this->getResource()));
        }

        foreach ($this->getAttributeDesignator() as $attrDesignator) {
            $attrDesignator->toXML($e);
        }

        return $e;
    }
}
