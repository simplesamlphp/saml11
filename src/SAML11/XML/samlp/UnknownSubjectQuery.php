<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\SAML11\XML\saml\Subject;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XMLSchema\Type\QNameValue;

/**
 * Class for unknown subject queries.
 *
 * @package simplesamlphp/saml11
 */
final class UnknownSubjectQuery extends AbstractSubjectQuery
{
    /**
     * @param \SimpleSAML\XML\Chunk $chunk The whole SubjectQuery element as a chunk object.
     * @param \SimpleSAML\XMLSchema\Type\QNameValue $type The xsi:type of this condition.
     */
    public function __construct(
        protected Chunk $chunk,
        QNameValue $type,
        Subject $subject,
    ) {
        parent::__construct($type, $subject);
    }


    /**
     * Get the raw version of this subject query as a Chunk.
     *
     * @return \SimpleSAML\XML\Chunk
     */
    public function getRawSubjectQuery(): Chunk
    {
        return $this->chunk;
    }


    /**
     * Convert this unknown subject query to XML.
     *
     * @param \DOMElement|null $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this unknown subject query.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        return $this->getRawSubjectQuery()->toXML($parent);
    }
}
