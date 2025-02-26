<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\SAML11\Type\SAMLDateTimeValue;
use SimpleSAML\SAML11\Utils\XPath;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\Type\{IDValue, NonNegativeIntegerValue};

use function array_pop;
use function strval;

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
     * @param \SimpleSAML\XML\Type\IDValue $id
     * @param \SimpleSAML\XML\Type\NonNegativeIntegerValue $majorVersion
     * @param \SimpleSAML\XML\Type\NonNegativeIntegerValue $minorVersion
     * @param \SimpleSAML\SAML11\Type\SAMLDateTimeValue $issueInstant
     * @param array<\SimpleSAML\SAML11\XML\samlp\RespondWith>
     *
     * @throws \Exception
     */
    protected function __construct(
        protected IDValue $id,
        protected NonNegativeIntegerValue $majorVersion,
        protected NonNegativeIntegerValue $minorVersion,
        protected SAMLDateTimeValue $issueInstant,
        protected array $respondWith = [],
    ) {
        Assert::allIsInstanceOf($respondWith, RespondWith::class, SchemaViolationException::class);

        parent::__construct($majorVersion, $minorVersion, $issueInstant);
    }


    /**
     * Retrieve the ID of this request.
     *
     * @return \SimpleSAML\XML\Type\IDValue The ID of this request
     */
    public function getID(): IDValue
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
        $e->setAttribute('RequestID', strval($this->getID()));

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
