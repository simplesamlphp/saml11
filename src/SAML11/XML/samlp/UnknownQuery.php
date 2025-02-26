<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\Type\QNameValue;

/**
 * Class for unknown queries.
 *
 * @package simplesamlphp/saml11
 */
final class UnknownQuery extends AbstractQuery
{
    /**
     * @param \SimpleSAML\XML\Chunk $chunk The whole Query element as a chunk object.
     * @param \SimpleSAML\XML\Type\QNameValue $type The xsi:type of this condition.
     */
    public function __construct(
        protected Chunk $chunk,
        QNameValue $type,
    ) {
        parent::__construct($type);
    }


    /**
     * Get the raw version of this query as a Chunk.
     *
     * @return \SimpleSAML\XML\Chunk
     */
    public function getRawQuery(): Chunk
    {
        return $this->chunk;
    }


    /**
     * Convert this unknown query to XML.
     *
     * @param \DOMElement|null $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this unknown query.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        return $this->getRawQuery()->toXML($parent);
    }
}
