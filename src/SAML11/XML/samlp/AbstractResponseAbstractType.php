<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\SAML11\Type\SAMLDateTimeValue;
use SimpleSAML\XMLSchema\Type\{IDValue, NonNegativeIntegerValue};

use function strval;

/**
 * Base class for all SAML 1.1 responses.
 *
 * Implements what is common between the samlp:RequestAbstractType and
 * samlp:ResponseAbstractType element types.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractResponseAbstractType extends AbstractMessage
{
    /**
     * Initialize a response.
     *
     * @param \SimpleSAML\XMLSchema\Type\IDValue $id
     * @param \SimpleSAML\XMLSchema\Type\NonNegativeIntegerValue $majorVersion
     * @param \SimpleSAML\XMLSchema\Type\NonNegativeIntegerValue $minorVersion
     * @param \SimpleSAML\SAML11\Type\SAMLDateTimeValue|null $issueInstant
     *
     * @throws \Exception
     */
    protected function __construct(
        protected IDValue $id,
        NonNegativeIntegerValue $majorVersion,
        NonNegativeIntegerValue $minorVersion,
        SAMLDateTimeValue $issueInstant,
    ) {
        parent::__construct($majorVersion, $minorVersion, $issueInstant);
    }


    /**
     * Retrieve the identifier of this message.
     *
     * @return \SimpleSAML\XMLSchema\Type\IDValue The identifier of this message
     */
    public function getID(): IDValue
    {
        return $this->id;
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
        $e->setAttribute('ResponseID', strval($this->getId()));

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
            $signedXML->appendChild($this->signature?->toXML($signedXML));

            return $signedXML;
        }

        return $e;
    }
}
