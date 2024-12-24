<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\XML\saml\Subject;
use SimpleSAML\XML\Exception\SchemaViolationException;

/**
 * Abstract class to be implemented by all the authentication queries in this namespace
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractAuthenticationQueryType extends AbstractSubjectQueryAbstractType
{
    /**
     * Initialize a samlp:AuthenticationQuery element.
     *
     * @param \SimpleSAML\SAML11\XML\saml\Subject $subject
     * @param string $authenticationMethod
     */
    public function __construct(
        Subject $subject,
        protected string $authenticationMethod,
    ) {
        Assert::validURI($authenticationMethod, SchemaViolationException::class);

        parent::__construct($subject);
    }


    /**
     * @return string
     */
    public function getAuthenticationMethod(): string
    {
        return $this->authenticationMethod;
    }


    /**
     * Convert this AuthenticationQuery to XML.
     *
     * @param \DOMElement $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this SubjectQuery.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = parent::toXML($parent);
        $e->setAttribute('AuthenticationMethod', $this->getAuthenticationMethod());

        return $e;
    }
}
