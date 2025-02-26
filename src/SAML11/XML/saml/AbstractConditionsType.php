<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Type\SAMLDateTimeValue;
use SimpleSAML\XML\Exception\InvalidDOMElementException;

use function strval;

/**
 * SAML ConditionsType abstract data type.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractConditionsType extends AbstractSamlElement
{
    /**
     * Initialize a saml:ConditionsType from scratch
     *
     * @param array<\SimpleSAML\SAML11\XML\saml\AudienceRestrictionCondition> $audienceRestrictionCondition
     * @param array<\SimpleSAML\SAML11\XML\saml\DoNotCacheCondition> $doNotCacheCondition
     * @param array<\SimpleSAML\SAML11\XML\saml\AbstractCondition> $condition
     * @param \SimpleSAML\SAML11\Type\SAMLDateTimeValue|null $notBefore
     * @param \SimpleSAML\SAML11\Type\SAMLDateTimeValue|null $notOnOrAfter
     */
    final public function __construct(
        protected array $audienceRestrictionCondition = [],
        protected array $doNotCacheCondition = [],
        protected array $condition = [],
        protected ?SAMLDateTimeValue $notBefore = null,
        protected ?SAMLDateTimeValue $notOnOrAfter = null,
    ) {
        Assert::allIsInstanceOf($audienceRestrictionCondition, AudienceRestrictionCondition::class);
        Assert::allIsInstanceOf($doNotCacheCondition, DoNotCacheCondition::class);
        Assert::allIsInstanceOf($condition, AbstractCondition::class);
    }


    /**
     * Collect the value of the notBefore-property
     *
     * @return \SimpleSAML\SAML11\Type\SAMLDateTimeValue|null
     */
    public function getNotBefore(): ?SAMLDateTimeValue
    {
        return $this->notBefore;
    }


    /**
     * Collect the value of the notOnOrAfter-property
     *
     * @return \SimpleSAML\SAML11\Type\SAMLDateTimeValue|null
     */
    public function getNotOnOrAfter(): ?SAMLDateTimeValue
    {
        return $this->notOnOrAfter;
    }


    /**
     * Collect the value of the audienceRestrictionCondition-property
     *
     * @return array<\SimpleSAML\SAML11\XML\saml\AudienceRestrictionCondition>
     */
    public function getAudienceRestrictionCondition(): array
    {
        return $this->audienceRestrictionCondition;
    }


    /**
     * Collect the value of the doNotCacheCondition-property
     *
     * @return array<\SimpleSAML\SAML11\XML\saml\DoNotCacheCondition>
     */
    public function getDoNotCacheCondition(): array
    {
        return $this->doNotCacheCondition;
    }


    /**
     * Collect the value of the condition-property
     *
     * @return array<\SimpleSAML\SAML11\XML\saml\Condition>
     */
    public function getCondition(): array
    {
        return $this->condition;
    }


    /**
     * Test if an object, at the state it's in, would produce an empty XML-element
     *
     * @return bool
     */
    public function isEmptyElement(): bool
    {
        return empty($this->getAudienceRestrictionCondition())
            && empty($this->getDoNotCacheCondition())
            && empty($this->getCondition())
            && empty($this->getNotBefore())
            && empty($this->getNotOnOrAfter());
    }


    /**
     * Convert XML into an ConditionsType
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

        return new static(
            AudienceRestrictionCondition::getChildrenOfClass($xml),
            DoNotCacheCondition::getChildrenOfClass($xml),
            AbstractCondition::getChildrenOfClass($xml),
            self::getOptionalAttribute($xml, 'NotBefore', SAMLDateTimeValue::class),
            self::getOptionalAttribute($xml, 'NotOnOrAfter', SAMLDateTimeValue::class),
        );
    }


    /**
     * Convert this ConditionsType to XML.
     *
     * @param \DOMElement $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this ConditionsType.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        if ($this->getNotBefore() !== null) {
            $e->setAttribute('NotBefore', strval($this->getNotBefore()));
        }

        if ($this->getNotOnOrAfter() !== null) {
            $e->setAttribute('NotOnOrAfter', strval($this->getNotOnOrAfter()));
        }

        foreach ($this->getAudienceRestrictionCondition() as $audienceRestrictionCondition) {
            $audienceRestrictionCondition->toXML($e);
        }

        foreach ($this->getDoNotCacheCondition() as $doNotCacheCondition) {
            $doNotCacheCondition->toXML($e);
        }

        foreach ($this->getCondition() as $condition) {
            $condition->toXML($e);
        }

        return $e;
    }
}
