<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DateTimeImmutable;
use DOMElement;
use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\SAML11\XML\saml\Assertion;
use SimpleSAML\SAML11\XML\samlp\Status;
use SimpleSAML\XML\Exception\SchemaViolationException;

/**
 * Base class for all SAML 1.1 samlp:AbstractResponseAbstractType.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractResponseType extends AbstractResponseAbstractType
{
    /**
     * Initialize a response.
     *
     * @param string $id
     * @param \SimpleSAML\SAML11\XML\samlp\Status $status
     * @param array<\SimpleSAML\SAML11\XML\saml\Assertion> $assertion
     * @param int $majorVersion
     * @param int $minorVersion
     * @param \DateTimeImmutable|null $issueInstant
     * @param string|null $inResponseTo
     * @param string|null $recipient
     *
     * @throws \Exception
     */
    public function __construct(
        string $id,
        protected Status $status,
        protected array $assertion = [],
        int $majorVersion = 1,
        int $minorVersion = 1,
        ?DateTimeImmutable $issueInstant = null,
        protected ?string $inResponseTo = null,
        protected ?string $recipient = null,
    ) {
        Assert::nullOrValidNCName($inResponseTo, SchemaViolationException::class);
        Assert::nullOrValidURI($recipient, SchemaViolationException::class);
        Assert::allIsInstanceOf($assertion, Assertion::class, SchemaViolationException::class);

        parent::__construct($id, $majorVersion, $minorVersion, $issueInstant);
    }


    /**
     * Retrieve the inResponseTo of this message.
     *
     * @return string|null The inResponseTo of this message
     */
    public function getInResponseTo(): ?string
    {
        return $this->inResponseTo;
    }


    /**
     * Retrieve the recipient of this message.
     *
     * @return string|null The recipient of this message
     */
    public function getRecipient(): ?string
    {
        return $this->recipient;
    }


    /**
     * Retrieve the assertion of this message.
     *
     * @return array<\SimpleSAML\SAML11\XML\saml\Assertion> The assertion of this message
     */
    public function getAssertion(): array
    {
        return $this->assertion;
    }


    /**
     * Retrieve the status of this message.
     *
     * @return \SimpleSAML\SAML11\XML\samlp\Status The status of this message
     */
    public function getStatus(): Status
    {
        return $this->status;
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

        if ($this->getRecipient() !== null) {
            $e->setAttribute('Recipient', $this->getRecipient());
        }

        if ($this->getInResponseTo() !== null) {
            $e->setAttribute('InResponseTo', $this->getInResponseTo());
        }

        $this->getStatus()->toXML($e);

        foreach ($this->getAssertion() as $assertion) {
            $assertion->toXML($e);
        }

        return $e;
    }
}
