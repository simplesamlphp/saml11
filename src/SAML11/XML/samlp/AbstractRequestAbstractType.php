<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DateTimeImmutable;
use DOMElement;
use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\SAML11\Utils\XPath;
use SimpleSAML\XML\Exception\SchemaViolationException;

use function array_pop;

/**
 * Base class for all SAML 1.1 requests.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractRequestAbstractType extends AbstractMessage
{
    /**
     * Initialize a request.
     *
     * @param string $id
     * @param int $majorVersion
     * @param int $minorVersion
     * @param \DateTimeImmutable $issueInstant
     * @param array<\SimpleSAML\SAML11\XML\samlp\RespondWith>
     *
     * @throws \Exception
     */
    protected function __construct(
        protected string $id,
        int $majorVersion,
        int $minorVersion,
        DateTimeImmutable $issueInstant,
        protected array $respondWith = [],
    ) {
        Assert::validNCName($id, SchemaViolationException::class);
        Assert::allIsInstanceOf($respondWith, RespondWith::class, SchemaViolationException::class);

        parent::__construct($majorVersion, $minorVersion, $issueInstant);
    }


    /**
     * Retrieve the ID of this request.
     *
     * @return string The ID of this request
     */
    public function getID(): string
    {
        return $this->id;
    }


    /**
     * @return array<\SimpleSAML\SAML11\XML\samlp\RespondWith>
     */
    public function getRespondWith(): array
    {
        return $this->respondWith;
    }


    /**
     * Convert this message to an unsigned XML document.
     * This method does not sign the resulting XML document.
     *
     * @return \DOMElement The root element of the DOM tree
     */
    protected function toUnsignedXML(?DOMElement $parent = null): DOMElement
    {
        $e = parent::toUnsignedXML($parent);
        $e->setAttribute('RequestID', $this->getID());

        foreach ($this->getRespondWith() as $respondWith) {
            $respondWith->toXML($e);
        }

        return $e;
    }


    /**
     * Create XML from this class
     *
     * @param \DOMElement|null $parent
     * @return \DOMElement
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        if ($this->isSigned() === true && $this->signer === null) {
            // We already have a signed document and no signer was set to re-sign it
            if ($parent === null) {
                return $this->xml;
            }

            $node = $parent->ownerDocument?->importNode($this->getXML(), true);
            $parent->appendChild($node);

            return $parent;
        }

        $e = $this->toUnsignedXML($parent);

        if ($this->signer !== null) {
            $signedXML = $this->doSign($e);

            // Test for an RespondWith
            $messageElements = XPath::xpQuery($signedXML, './saml_protocol:RespondWith', XPath::getXPath($signedXML));
            $respondWith = array_pop($messageElements);

            if ($respondWith === null) {
                $signedXML->appendChild($this->signature?->toXML($signedXML));
            } else {
                $signedXML->insertBefore($this->signature?->toXML($signedXML), $respondWith->nextSibling);
            }

            return $signedXML;
        }

        return $e;
    }
}
