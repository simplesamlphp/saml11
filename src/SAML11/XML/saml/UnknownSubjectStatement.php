<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XMLSchema\Type\QNameValue;

/**
 * Class for unknown SubjectStatements.
 *
 * @package simplesamlphp/saml11
 */
final class UnknownSubjectStatement extends AbstractSubjectStatement
{
    /**
     * @param \SimpleSAML\XML\Chunk $chunk The whole SubjectStatement element as a chunk object.
     * @param \SimpleSAML\XMLSchema\Type\QNameValue $type The xsi:type of this SubjectStatement
     */
    public function __construct(
        protected Chunk $chunk,
        QNameValue $type,
        protected Subject $subject,
    ) {
        parent::__construct($type, $subject);
    }


    /**
     * Get the raw version of this SubjectStatement as a Chunk.
     *
     * @return \SimpleSAML\XML\Chunk
     */
    public function getRawSubjectStatement(): Chunk
    {
        return $this->chunk;
    }


    /**
     * Convert this unknown SubjectStatement to XML.
     *
     * @param \DOMElement|null $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this unknown SubjectStatement.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        return $this->getRawSubjectStatement()->toXML($parent);
    }
}
