<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Exception\ProtocolViolationException;
use SimpleSAML\XMLSchema\Exception\{InvalidDOMElementException, MissingElementException, TooManyElementsException};

use function array_pop;

/**
 * SAML Status data type.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractStatusType extends AbstractSamlpElement
{
    /**
     * Initialize a samlp:Status
     *
     * @param \SimpleSAML\SAML11\XML\samlp\StatusCode $statusCode
     * @param \SimpleSAML\SAML11\XML\samlp\StatusMessage|null $statusMessage
     * @param \SimpleSAML\SAML11\XML\samlp\StatusDetail|null $statusDetail
     */
    final public function __construct(
        protected StatusCode $statusCode,
        protected ?StatusMessage $statusMessage = null,
        protected ?StatusDetail $statusDetail = null,
    ) {
        Assert::oneOf(
            $statusCode->getValue()->getValue(),
            C::$STATUS_CODES,
            'Invalid top-level status code:  %s',
            ProtocolViolationException::class,
        );
    }


    /**
     * Collect the StatusCode
     *
     * @return \SimpleSAML\SAML11\XML\samlp\StatusCode
     */
    public function getStatusCode(): StatusCode
    {
        return $this->statusCode;
    }


    /**
     * Collect the value of the statusMessage
     *
     * @return \SimpleSAML\SAML11\XML\samlp\StatusMessage|null
     */
    public function getStatusMessage(): ?StatusMessage
    {
        return $this->statusMessage;
    }


    /**
     * Collect the value of the statusDetails property
     *
     * @return \SimpleSAML\SAML11\XML\samlp\StatusDetail|null
     */
    public function getStatusDetail(): ?StatusDetail
    {
        return $this->statusDetail;
    }


    /**
     * Convert XML into a Status
     *
     * @param \DOMElement $xml The XML element we should load
     * @return static
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     * @throws \SimpleSAML\XML\Exception\TooManyElementsException
     *   if too many child-elements of a type are specified
     * @throws \SimpleSAML\XML\Exception\MissingElementException
     *   if one of the mandatory child-elements is missing
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'Status', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, Status::NS, InvalidDOMElementException::class);

        $statusCode = StatusCode::getChildrenOfClass($xml);
        Assert::minCount($statusCode, 1, MissingElementException::class);
        Assert::maxCount($statusCode, 1, TooManyElementsException::class);

        $statusMessage = StatusMessage::getChildrenOfClass($xml);
        Assert::maxCount($statusMessage, 1, TooManyElementsException::class);

        $statusDetail = StatusDetail::getChildrenOfClass($xml);

        return new static(
            array_pop($statusCode),
            array_pop($statusMessage),
            array_pop($statusDetail),
        );
    }


    /**
     * Convert this Status to XML.
     *
     * @param \DOMElement|null $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this Status.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        $this->getStatusCode()->toXML($e);

        $this->getStatusMessage()?->toXML($e);

        $this->getStatusDetail()?->toXML($e);

        return $e;
    }
}
