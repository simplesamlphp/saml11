<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Assert\Assert as SAMLAssert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\MissingElementException;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\Exception\TooManyElementsException;

/**
 * SAML AuthorizationDecisionStatementType abstract data type.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractAuthorizationDecisionStatementType extends AbstractSubjectStatementType
{
    /**
     * Initialize a saml:AuthorizationDecisionStatementType from scratch
     *
     * @param string $resource
     * @param \SimpleSAML\SAML11\XML\saml\DecisionTypeEnum $decision
     * @param \SimpleSAML\SAML11\XML\saml\Subject $subject
     * @param array<\SimpleSAML\SAML11\XML\saml\Action> $action
     * @param \SimpleSAML\SAML11\XML\saml\Evidence|null $evidence
     */
    final public function __construct(
        Subject $subject,
        protected string $resource,
        protected DecisionTypeEnum $decision,
        protected array $action = [],
        protected ?Evidence $evidence = null,
    ) {
        SAMLAssert::validURI($resource);
        Assert::minCount($action, 1, MissingElementException::class);
        Assert::allIsInstanceOf($action, Action::class, SchemaViolationException::class);

        parent::__construct($subject);
    }


    /**
     * Collect the value of the resource-property
     *
     * @return string
     */
    public function getResource(): string
    {
        return $this->resource;
    }


    /**
     * Collect the value of the decision-property
     *
     * @return \SimpleSAML\SAML11\XML\saml\DecisionTypeEnum
     */
    public function getDecision(): DecisionTypeEnum
    {
        return $this->decision;
    }


    /**
     * Collect the value of the evidence-property
     *
     * @return \SimpleSAML\SAML11\XML\saml\Evidence|null
     */
    public function getEvidence(): ?Evidence
    {
        return $this->evidence;
    }


    /**
     * Collect the value of the action-property
     *
     * @return array<\SimpleSAML\SAML11\XML\saml\Action>
     */
    public function getAction(): array
    {
        return $this->action;
    }


    /**
     * Convert XML into an AuthorizationDecisionStatementType
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

        $evidence = Evidence::getChildrenOfClass($xml);
        Assert::maxCount($evidence, 1, TooManyElementsException::class);

        return new static(
            array_pop($subject),
            self::getAttribute($xml, 'Resource'),
            DecisionTypeEnum::from(self::getAttribute($xml, 'Decision')),
            Action::getChildrenOfClass($xml),
            array_pop($evidence),
        );
    }


    /**
     * Convert this AuthorizationDecisionStatementType to XML.
     *
     * @param \DOMElement $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data
     *   corresponding to this AuthorizationDecisionStatementType.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = parent::toXML($parent);

        $e->setAttribute('Resource', $this->getResource());
        $e->setAttribute('Decision', $this->getDecision()->value);

        foreach ($this->getAction() as $action) {
            $action->toXML($e);
        }

        $this->getEvidence()?->toXML($e);

        return $e;
    }
}
