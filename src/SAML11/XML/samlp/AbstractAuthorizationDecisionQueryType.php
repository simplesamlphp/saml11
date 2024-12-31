<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\XML\saml\Action;
use SimpleSAML\SAML11\XML\saml\Evidence;
use SimpleSAML\SAML11\XML\saml\Subject;
use SimpleSAML\XML\Exception\MissingElementException;
use SimpleSAML\XML\Exception\SchemaViolationException;

/**
 * Abstract class to be implemented by all the authorization decision queries in this namespace
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractAuthorizationDecisionQueryType extends AbstractSubjectQueryAbstractType
{
    /**
     * Initialize a samlp:AuthorizationDecisionQuery element.
     *
     * @param \SimpleSAML\SAML11\XML\saml\Subject $subject
     * @param string $resource
     * @param \SimpleSAML\SAML11\XML\saml\Evidence|null $evidence
     * @param array<\SimpleSAML\SAML11\XML\saml\Action> $action
     */
    public function __construct(
        Subject $subject,
        protected string $resource,
        protected ?Evidence $evidence = null,
        protected array $action = [],
    ) {
        Assert::validURI($resource, SchemaViolationException::class);
        Assert::allIsInstanceOf($action, Action::class, SchemaViolationException::class);
        Assert::minCount($action, 1, MissingElementException::class);

        parent::__construct($subject);
    }


    /**
     * @return string
     */
    public function getResource(): string
    {
        return $this->resource;
    }


    /**
     * @return \SimpleSAML\SAML11\XML\saml\Evidence|null
     */
    public function getEvidence(): ?Evidence
    {
        return $this->evidence;
    }


    /**
     * @return array<\SimpleSAML\SAML11\XML\saml\Action>
     */
    public function getAction(): array
    {
        return $this->action;
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
        $e->setAttribute('Resource', $this->getResource());

        foreach ($this->getAction() as $action) {
            $action->toXML($e);
        }

        $this->getEvidence()?->toXML($e);

        return $e;
    }
}
