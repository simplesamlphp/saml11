<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\SAML11\Type\SAMLAnyURIValue;
use SimpleSAML\SAML11\XML\saml\Subject;

use function strval;

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
     * @param \SimpleSAML\SAML11\Type\SAMLAnyURIValue $authenticationMethod
     */
    public function __construct(
        Subject $subject,
        protected SAMLAnyURIValue $authenticationMethod,
    ) {
        parent::__construct($subject);
    }


    /**
     * @return \SimpleSAML\SAML11\Type\SAMLAnyURIValue
     */
    public function getAuthenticationMethod(): SAMLAnyURIValue
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
        $e->setAttribute('AuthenticationMethod', strval($this->getAuthenticationMethod()));

        return $e;
    }
}
