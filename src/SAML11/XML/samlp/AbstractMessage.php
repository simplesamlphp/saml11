<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DateTimeImmutable;
use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Exception\ProtocolViolationException;
use SimpleSAML\SAML11\Utils;
use SimpleSAML\SAML11\XML\SignableElementTrait;
use SimpleSAML\SAML11\XML\SignedElementTrait;
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


    /** @var bool */
    protected bool $messageContainedSignatureUponConstruction = false;

    /**
     * The original signed XML
     *
     * @var \DOMElement
     */
    protected DOMElement $xml;


    /**
     * Initialize a message.
     *
     * @param int $majorVersion
     * @param int $minorVersion
     * @param \DateTimeImmutable $issueInstant
     *
     * @throws \Exception
     */
    protected function __construct(
        protected int $majorVersion,
        protected int $minorVersion,
        protected ?DateTimeImmutable $issueInstant,
    ) {
        Assert::nullOrSame($issueInstant?->getTimeZone()->getName(), 'Z', ProtocolViolationException::class);
    }


    /**
     * Retrieve the major version of this message.
     *
     * @return int The major version of this message
     */
    public function getMajorVersion(): int
    {
        return $this->majorVersion;
    }


    /**
     * Retrieve the minor version of this message.
     *
     * @return int The minor version of this message
     */
    public function getMinorVersion(): int
    {
        return $this->minorVersion;
    }


    /**
     * Retrieve the issue timestamp of this message.
     *
     * @return \DateTimeImmutable The issue timestamp of this message, as an UNIX timestamp
     */
    public function getIssueInstant(): DateTimeImmutable
    {
        if ($this->issueInstant === null) {
            return Utils::getContainer()->getClock()->now();
        }

        return $this->issueInstant;
    }


    /**
     * Query whether or not the message contained a signature at the root level when the object was constructed.
     *
     * @return bool
     */
    public function isMessageConstructedWithSignature(): bool
    {
        return $this->messageContainedSignatureUponConstruction;
    }


    /**
     * Get the XML element.
     *
     * @return \DOMElement
     */
    public function getXML(): DOMElement
    {
        return $this->xml;
    }


    /**
     * Set the XML element.
     *
     * @param \DOMElement $xml
     */
    protected function setXML(DOMElement $xml): void
    {
        $this->xml = $xml;
    }


    /**
     * @return \DOMElement
     */
    protected function getOriginalXML(): DOMElement
    {
        return $this->xml ?? $this->toUnsignedXML();
    }


    /**
     * Convert this message to an unsigned XML document.
     * This method does not sign the resulting XML document.
     *
     * @return \DOMElement The root element of the DOM tree
     */
    protected function toUnsignedXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        /* Ugly hack to add another namespace declaration to the root element. */
        $e->setAttributeNS(C::NS_SAML, 'saml:tmp', 'tmp');
        $e->removeAttributeNS(C::NS_SAML, 'tmp');

        $e->setAttribute('MajorVersion', strval($this->getMajorVersion()));
        $e->setAttribute('MinorVersion', strval($this->getMinorVersion()));
        $e->setAttribute('IssueInstant', $this->getIssueInstant()->format(C::DATETIME_FORMAT));

        return $e;
    }
}
