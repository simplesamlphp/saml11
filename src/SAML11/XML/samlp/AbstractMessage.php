<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\SAML11\Type\SAMLDateTimeValue;
use SimpleSAML\SAML11\XML\SignableElementTrait;
use SimpleSAML\SAML11\XML\SignedElementTrait;
use SimpleSAML\XMLSchema\Type\NonNegativeIntegerValue;
use SimpleSAML\XMLSecurity\XML\SignableElementInterface;
use SimpleSAML\XMLSecurity\XML\SignedElementInterface;

use function strval;

/**
 * Base class for all SAML 1.1 messages.
 *
 * Implements what is common between the samlp:RequestAbstractType and
 * samlp:ResponseAbstractType element types.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractMessage extends AbstractSamlpElement implements SignableElementInterface, SignedElementInterface
{
    use SignableElementTrait;
    use SignedElementTrait {
        SignedElementTrait::getBlacklistedAlgorithms insteadof SignableElementTrait;
    }


    protected bool $messageContainedSignatureUponConstruction = false;

    /**
     * The original signed XML
     */
    protected DOMElement $xml;


    /**
     * Initialize a message.
     *
     * @param \SimpleSAML\XMLSchema\Type\NonNegativeIntegerValue $majorVersion
     * @param \SimpleSAML\XMLSchema\Type\NonNegativeIntegerValue $minorVersion
     * @param \SimpleSAML\SAML11\Type\SAMLDateTimeValue $issueInstant
     *
     * @throws \Exception
     */
    protected function __construct(
        protected NonNegativeIntegerValue $majorVersion,
        protected NonNegativeIntegerValue $minorVersion,
        protected SAMLDateTimeValue $issueInstant,
    ) {
    }


    /**
     * Retrieve the major version of this message.
     *
     * @return \SimpleSAML\XMLSchema\Type\NonNegativeIntegerValue The major version of this message
     */
    public function getMajorVersion(): NonNegativeIntegerValue
    {
        return $this->majorVersion;
    }


    /**
     * Retrieve the minor version of this message.
     *
     * @return \SimpleSAML\XMLSchema\Type\NonNegativeIntegerValue The minor version of this message
     */
    public function getMinorVersion(): NonNegativeIntegerValue
    {
        return $this->minorVersion;
    }


    /**
     * Retrieve the issue timestamp of this message.
     *
     * @return \SimpleSAML\SAML11\Type\SAMLDateTimeValue The issue timestamp of this message
     */
    public function getIssueInstant(): SAMLDateTimeValue
    {
        return $this->issueInstant;
    }


    /**
     * Query whether or not the message contained a signature at the root level when the object was constructed.
     */
    public function isMessageConstructedWithSignature(): bool
    {
        return $this->messageContainedSignatureUponConstruction;
    }


    /**
     * Get the XML element.
     */
    public function getXML(): DOMElement
    {
        return $this->xml;
    }


    /**
     * Set the XML element.
     */
    protected function setXML(DOMElement $xml): void
    {
        $this->xml = $xml;
    }


    /**
     */
    protected function getOriginalXML(): DOMElement
    {
        return $this->xml ?? $this->toUnsignedXML();
    }


    /**
     * Convert this message to an unsigned XML document.
     * This method does not sign the resulting XML document.
     */
    protected function toUnsignedXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        $e->setAttribute('MajorVersion', strval($this->getMajorVersion()));
        $e->setAttribute('MinorVersion', strval($this->getMinorVersion()));
        $e->setAttribute('IssueInstant', strval($this->getIssueInstant()));

        return $e;
    }
}
