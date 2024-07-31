<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DateTimeImmutable;
use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Assert\Assert as SAMLAssert;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Exception\ProtocolViolationException;
use SimpleSAML\XML\Exception\InvalidDOMElementException;

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
     * @param \DateTimeImmutable|null $notBefore
     * @param \DateTimeImmutable|null $notOnOrAfter
     */
    final public function __construct(
        protected array $audienceRestrictionCondition = [],
        protected array $doNotCacheCondition = [],
        protected array $condition = [],
        protected ?DateTimeImmutable $notBefore = null,
        protected ?DateTimeImmutable $notOnOrAfter = null,
    ) {
        Assert::allIsInstanceOf($audienceRestrictionCondition, AudienceRestrictionCondition::class);
        Assert::allIsInstanceOf($doNotCacheCondition, DoNotCacheCondition::class);
        Assert::allIsInstanceOf($condition, AbstractCondition::class);
    }


    /**
     * Collect the value of the notBefore-property
     *
     * @return \DateTimeImmutable|null
     */
    public function getNotBefore(): ?DateTimeImmutable
    {
        return $this->notBefore;
    }


    /**
     * Collect the value of the notOnOrAfter-property
     *
     * @return \DateTimeImmutable|null
     */
    public function getNotOnOrAfter(): ?DateTimeImmutable
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

        $notBefore = self::getOptionalAttribute($xml, 'NotBefore');
        // Strip sub-seconds - See paragraph 1.2.2 of SAML core specifications
        $notBefore = preg_replace('/([.][0-9]+Z)$/', 'Z', $notBefore, 1);

        SAMLAssert::validDateTime($notBefore, ProtocolViolationException::class);
        $notBefore = new DateTimeImmutable($notBefore);

        $notOnOrAfter = self::getOptionalAttribute($xml, 'NotOnOrAfter');
        // Strip sub-seconds - See paragraph 1.2.2 of SAML core specifications
        $notOnOrAfter = preg_replace('/([.][0-9]+Z)$/', 'Z', $notOnOrAfter, 1);

        SAMLAssert::validDateTime($notOnOrAfter, ProtocolViolationException::class);
        $notOnOrAfter = new DateTimeImmutable($notOnOrAfter);

        $audienceRestrictionCondition = AudienceRestrictionCondition::getChildrenOfClass($xml);
        $doNotCacheCondition = DoNotCacheCondition::getChildrenOfClass($xml);
        $condition = AbstractCondition::getChildrenOfClass($xml);

        return new static($audienceRestrictionCondition, $doNotCacheCondition, $condition, $notBefore, $notOnOrAfter);
    }


    /**
     * Convert this ConditionsType to XML.
     *
     * @param \DOMElement $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this ConditionsType.
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        if ($this->getNotBefore() !== null) {
            $e->setAttribute('NotBefore', $this->getNotBefore()->format(C::DATETIME_FORMAT));
        }

        if ($this->getNotOnOrAfter() !== null) {
            $e->setAttribute('NotOnOrAfter', $this->getNotOnOrAfter()->format(C::DATETIME_FORMAT));
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
