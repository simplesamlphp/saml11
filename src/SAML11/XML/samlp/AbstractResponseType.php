<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\SAML11\Type\{AnyURIValue, DateTimeValue};
use SimpleSAML\SAML11\XML\saml\Assertion;
use SimpleSAML\SAML11\XML\samlp\Status;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\Type\{IDValue, NCNameValue, NonNegativeIntegerValue};

use function strval;

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
     * @param \SimpleSAML\XML\Type\NonNegativeIntegerValue $majorVersion
     * @param \SimpleSAML\XML\Type\NonNegativeIntegerValue $minorVersion
     * @param \SimpleSAML\XML\Type\IDValue $id
     * @param \SimpleSAML\SAML11\XML\samlp\Status $status
     * @param array<\SimpleSAML\SAML11\XML\saml\Assertion> $assertion
     * @param \SimpleSAML\SAML11\Type\DateTimeValue|null $issueInstant
     * @param \SimpleSAML\XML\Type\NCNameValue|null $inResponseTo
     * @param \SimpleSAML\SAML11\Type\AnyURIValue|null $recipient
     *
     * @throws \Exception
     */
    public function __construct(
        NonNegativeIntegerValue $majorVersion,
        NonNegativeIntegerValue $minorVersion,
        IDValue $id,
        protected Status $status,
        protected array $assertion = [],
        ?DateTimeValue $issueInstant = null,
        protected ?NCNameValue $inResponseTo = null,
        protected ?AnyURIValue $recipient = null,
    ) {
        Assert::allIsInstanceOf($assertion, Assertion::class, SchemaViolationException::class);

        parent::__construct($id, $majorVersion, $minorVersion, $issueInstant);
    }


    /**
     * Retrieve the inResponseTo of this message.
     *
     * @return \SimpleSAML\XML\Type\NCNameValue|null The inResponseTo of this message
     */
    public function getInResponseTo(): ?NCNameValue
    {
        return $this->inResponseTo;
    }


    /**
     * Retrieve the recipient of this message.
     *
     * @return \SimpleSAML\SAML11\Type\AnyURIValue|null The recipient of this message
     */
    public function getRecipient(): ?AnyURIValue
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
            $e->setAttribute('Recipient', strval($this->getRecipient()));
        }

        if ($this->getInResponseTo() !== null) {
            $e->setAttribute('InResponseTo', strval($this->getInResponseTo()));
        }

        $this->getStatus()->toXML($e);

        foreach ($this->getAssertion() as $assertion) {
            $assertion->toXML($e);
        }

        return $e;
    }
}
