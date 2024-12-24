<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\XML\samlp\AbstractQuery;
use SimpleSAML\SAML11\XML\samlp\StatusMessage;
use SimpleSAML\XML\Exception\InvalidDOMElementException;

/**
 * Example class to demonstrate how Query can be extended.
 *
 * @package simplesamlphp\saml11
 */
final class CustomQuery extends AbstractQuery
{
    /** @var string */
    protected const XSI_TYPE_NAME = 'CustomQueryType';

    /** @var string */
    protected const XSI_TYPE_NAMESPACE = 'urn:x-simplesamlphp:namespace';

    /** @var string */
    protected const XSI_TYPE_PREFIX = 'ssp';


    /**
     * CustomQuery constructor.
     *
     * @param \SimpleSAML\SAML11\XML\samlp\StatusMessage[] $statusMessage
     */
    public function __construct(
        protected array $statusMessage,
    ) {
        Assert::allIsInstanceOf($statusMessage, StatusMessage::class);

        parent::__construct(self::XSI_TYPE_PREFIX . ':' . self::XSI_TYPE_NAME);
    }


    /**
     * Get the value of the statusMessage-attribute.
     *
     * @return \SimpleSAML\SAML11\XML\samlp\StatusMessage[]
     */
    public function getStatusMessage(): array
    {
        return $this->statusMessage;
    }


    /**
     * Convert XML into a Query
     *
     * @param \DOMElement $xml The XML element we should load
     * @return static
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'Query', InvalidDOMElementException::class);
        Assert::notNull($xml->namespaceURI, InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, AbstractQuery::NS, InvalidDOMElementException::class);
        Assert::true(
            $xml->hasAttributeNS(C::NS_XSI, 'type'),
            'Missing required xsi:type in <samlp:Query> element.',
            InvalidDOMElementException::class,
        );

        $type = $xml->getAttributeNS(C::NS_XSI, 'type');
        Assert::same($type, self::XSI_TYPE_PREFIX . ':' . self::XSI_TYPE_NAME);

        $statusMessage = StatusMessage::getChildrenOfClass($xml);

        return new static($statusMessage);
    }


    /**
     * Convert this Query to XML.
     *
     * @param \DOMElement $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this Query.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = parent::toXML($parent);

        foreach ($this->getStatusMessage() as $statusMessage) {
            $statusMessage->toXML($e);
        }

        return $e;
    }
}
