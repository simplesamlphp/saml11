<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\MissingElementException;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\Exception\TooManyElementsException;

/**
 * SAML AttributeStatementType abstract data type.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractAttributeStatementType extends AbstractSubjectStatementType
{
    /**
     * Initialize a saml:AttributeStatementType from scratch
     *
     * @param \SimpleSAML\SAML11\XML\saml\Subject $subject
     * @param array<\SimpleSAML\SAML11\XML\saml\Attribute> $attribute
     */
    public function __construct(
        Subject $subject,
        protected array $attribute = [],
    ) {
        Assert::allIsInstanceOf($attribute, Attribute::class, SchemaViolationException::class);

        parent::__construct($subject);
    }


    /**
     * Collect the value of the attribute-property
     *
     * @return array<\SimpleSAML\SAML11\XML\saml\Attribute>
     */
    public function getAttributes(): array
    {
        return $this->attribute;
    }


    /**
     * Convert XML into an AttributeStatementType
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

        $subject = Subject::getChildrenOfClass($xml);
        Assert::minCount($subject, 1, MissingElementException::class);
        Assert::maxCount($subject, 1, TooManyElementsException::class);

        return new static(
            array_pop($subject),
            Attribute::getChildrenOfClass($xml),
        );
    }


    /**
     * Convert this AttributeStatementType to XML.
     *
     * @param \DOMElement $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this AttributeStatementType.
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = parent::toXML($parent);

        foreach ($this->getAttributes() as $attr) {
            $attr->toXML($e);
        }

        return $e;
    }
}
