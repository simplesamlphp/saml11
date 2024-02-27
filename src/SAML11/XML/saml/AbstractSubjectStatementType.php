<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\XML\saml\AbstractStatementType;
use SimpleSAML\SAML11\XML\saml\Subject;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\MissingElementException;
use SimpleSAML\XML\Exception\TooManyElementsException;

use function array_pop;

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
     * Convert XML into a SubjectStatement
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

        $subject = Subject::getChildrenOfClass($xml);
        Assert::minCount($subject, 1, MissingElementException::class);
        Assert::maxCount($subject, 1, TooManyElementsException::class);

        return new static(
            array_pop($subject),
        );
    }


    /**
     * Convert this SubjectStatement to XML.
     *
     * @param \DOMElement $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this SubjectStatement.
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        $this->getSubject()->toXML($e);

        return $e;
    }
}
