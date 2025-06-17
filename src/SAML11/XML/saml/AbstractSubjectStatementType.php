<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;

/**
 * @package simplesamlphp\saml11
 */
abstract class AbstractSubjectStatementType extends AbstractStatementType
{
    /**
     * CustomStatement constructor.
     *
     * @param \SimpleSAML\SAML11\XML\saml\Subject $subject
     */
    public function __construct(
        protected Subject $subject,
    ) {
    }


    /**
     * Get the value of the subject-attribute.
     *
     * @return \SimpleSAML\SAML11\XML\saml\Subject
     */
    public function getSubject(): Subject
    {
        return $this->subject;
    }


    /**
     * Convert this SubjectStatement to XML.
     *
     * @param \DOMElement $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this SubjectStatement.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        $this->getSubject()->toXML($e);

        return $e;
    }
}
