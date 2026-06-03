<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use Dom;

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
     */
    public function toXML(?Dom\Element $parent = null): Dom\Element
    {
        $e = $this->instantiateParentElement($parent);

        $this->getSubject()->toXML($e);

        return $e;
    }
}
