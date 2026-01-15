<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\SAML11\Type\SAMLAnyURIValue;
use SimpleSAML\SAML11\Type\SAMLDateTimeValue;
use SimpleSAML\SAML11\XML\saml\Assertion;
use SimpleSAML\SAML11\XML\samlp\Status;
use SimpleSAML\XMLSchema\Exception\SchemaViolationException;
use SimpleSAML\XMLSchema\Type\IDValue;
use SimpleSAML\XMLSchema\Type\NCNameValue;
use SimpleSAML\XMLSchema\Type\NonNegativeIntegerValue;

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
     * @param \SimpleSAML\XMLSchema\Type\NonNegativeIntegerValue $majorVersion
     * @param \SimpleSAML\XMLSchema\Type\NonNegativeIntegerValue $minorVersion
     * @param \SimpleSAML\XMLSchema\Type\IDValue $id
     * @param \SimpleSAML\SAML11\XML\samlp\Status $status
     * @param array<\SimpleSAML\SAML11\XML\saml\Assertion> $assertion
     * @param \SimpleSAML\SAML11\Type\SAMLDateTimeValue|null $issueInstant
     * @param \SimpleSAML\XMLSchema\Type\NCNameValue|null $inResponseTo
     * @param \SimpleSAML\SAML11\Type\SAMLAnyURIValue|null $recipient
     *
     * @throws \Exception
     */
    public function __construct(
        NonNegativeIntegerValue $majorVersion,
        NonNegativeIntegerValue $minorVersion,
        IDValue $id,
        protected Status $status,
        SAMLDateTimeValue $issueInstant,
        protected array $assertion = [],
        protected ?NCNameValue $inResponseTo = null,
        protected ?SAMLAnyURIValue $recipient = null,
    ) {
        Assert::allIsInstanceOf($assertion, Assertion::class, SchemaViolationException::class);

        parent::__construct($id, $majorVersion, $minorVersion, $issueInstant);
    }


    /**
     * Retrieve the inResponseTo of this message.
     *
     * @return \SimpleSAML\XMLSchema\Type\NCNameValue|null The inResponseTo of this message
     */
    public function getInResponseTo(): ?NCNameValue
    {
        return $this->inResponseTo;
    }


    /**
     * Retrieve the recipient of this message.
     *
     * @return \SimpleSAML\SAML11\Type\SAMLAnyURIValue|null The recipient of this message
     */
    public function getRecipient(): ?SAMLAnyURIValue
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
