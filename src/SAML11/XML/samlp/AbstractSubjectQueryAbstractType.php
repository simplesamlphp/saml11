<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\SAML11\XML\saml\Subject;

/**
 * Abstract class to be implemented by all the subject queries in this namespace
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractSubjectQueryAbstractType extends AbstractQueryAbstractType
{
    /**
     * Initialize a samlp:SubjectQuery element.
     *
     * @param \SimpleSAML\SAML11\XML\saml\Subject $subject
     */
    protected function __construct(
        protected Subject $subject,
    ) {
    }


    /**
     * @return \SimpleSAML\SAML11\XML\saml\Subject
     */
    public function getSubject(): Subject
    {
        return $this->subject;
    }


    /**
     * Convert this SubjectQuery to XML.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        $this->getSubject()->toXML($e);

        return $e;
    }
}
