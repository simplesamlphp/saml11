<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSchema\Exception\MissingElementException;
use SimpleSAML\XMLSchema\Exception\SchemaViolationException;

/**
 * @package simplesamlphp\saml11
 */
abstract class AbstractAudienceRestrictionConditionType extends AbstractConditionType
{
    /**
     * AudienceRestrictionConditionType constructor.
     *
     * @param \SimpleSAML\SAML11\XML\saml\Audience[] $audience
     */
    final public function __construct(
        protected array $audience,
    ) {
        Assert::allIsInstanceOf($audience, Audience::class, SchemaViolationException::class);
    }


    /**
     * Get the value of the audience-attribute.
     *
     * @return \SimpleSAML\SAML11\XML\saml\Audience[]
     */
    public function getAudience(): array
    {
        return $this->audience;
    }


    /**
     * Convert XML into a AudienceRestrictionCondition
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

        $audience = Audience::getChildrenOfClass($xml);
        Assert::minCount($audience, 1, MissingElementException::class);

        return new static(
            $audience,
        );
    }


    /**
     * Convert this AudienceRestrictionCondition to XML.
     *
     * @param \DOMElement $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this AudienceRestrictionCondition.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        foreach ($this->getAudience() as $a) {
            $a->toXML($e);
        }

        return $e;
    }
}
