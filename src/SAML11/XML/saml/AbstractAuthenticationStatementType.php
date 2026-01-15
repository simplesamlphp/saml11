<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Type\SAMLAnyURIValue;
use SimpleSAML\SAML11\Type\SAMLDateTimeValue;
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSchema\Exception\MissingElementException;
use SimpleSAML\XMLSchema\Exception\SchemaViolationException;
use SimpleSAML\XMLSchema\Exception\TooManyElementsException;

/**
 * SAML AuthenticationStatementType abstract data type.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractAuthenticationStatementType extends AbstractSubjectStatementType
{
    /**
     * Initialize a saml:AuthenticationStatementType from scratch
     *
     * @param \SimpleSAML\SAML11\XML\saml\Subject $subject
     * @param \SimpleSAML\SAML11\Type\SAMLAnyURIValue $authenticationMethod
     * @param \SimpleSAML\SAML11\Type\SAMLDateTimeValue $authenticationInstant
     * @param \SimpleSAML\SAML11\XML\saml\SubjectLocality|null $subjectLocality
     * @param array<\SimpleSAML\SAML11\XML\saml\AuthorityBinding> $authorityBinding
     */
    final public function __construct(
        Subject $subject,
        protected SAMLAnyURIValue $authenticationMethod,
        protected SAMLDateTimeValue $authenticationInstant,
        protected ?SubjectLocality $subjectLocality = null,
        protected array $authorityBinding = [],
    ) {
        Assert::allIsInstanceOf($authorityBinding, AuthorityBinding::class, SchemaViolationException::class);

        parent::__construct($subject);
    }


    /**
     * Collect the value of the authorityBinding-property
     *
     * @return array<\SimpleSAML\SAML11\XML\saml\AuthorityBinding>
     */
    public function getAuthorityBinding(): array
    {
        return $this->authorityBinding;
    }


    /**
     * Collect the value of the subjectLocality-property
     *
     * @return \SimpleSAML\SAML11\XML\saml\SubjectLocality|null
     */
    public function getSubjectLocality(): ?SubjectLocality
    {
        return $this->subjectLocality;
    }


    /**
     * Collect the value of the authenticationMethod-property
     *
     * @return \SimpleSAML\SAML11\Type\SAMLAnyURIValue
     */
    public function getAuthenticationMethod(): SAMLAnyURIValue
    {
        return $this->authenticationMethod;
    }


    /**
     * Collect the value of the authenticationInstant-property
     *
     * @return \SimpleSAML\SAML11\Type\SAMLDateTimeValue
     */
    public function getAuthenticationInstant(): SAMLDateTimeValue
    {
        return $this->authenticationInstant;
    }


    /**
     * Convert XML into an AuthenticationStatementType
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, static::getLocalName(), InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, static::NS, InvalidDOMElementException::class);

        $authorityBinding = AuthorityBinding::getChildrenOfClass($xml);
        $subjectLocality = SubjectLocality::getChildrenOfClass($xml);
        Assert::maxCount($subjectLocality, 1, TooManyElementsException::class);

        $subject = Subject::getChildrenOfClass($xml);
        Assert::minCount($subject, 1, MissingElementException::class);
        Assert::maxCount($subject, 1, TooManyElementsException::class);

        return new static(
            array_pop($subject),
            self::getAttribute($xml, 'AuthenticationMethod', SAMLAnyURIValue::class),
            self::getAttribute($xml, 'AuthenticationInstant', SAMLDateTimeValue::class),
            array_pop($subjectLocality),
            $authorityBinding,
        );
    }


    /**
     * Convert this AuthenticationStatementType to XML.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = parent::toXML($parent);

        $e->setAttribute('AuthenticationMethod', strval($this->getAuthenticationMethod()));
        $e->setAttribute('AuthenticationInstant', strval($this->getAuthenticationInstant()));

        $this->getSubjectLocality()?->toXML($e);

        foreach ($this->getAuthorityBinding() as $ab) {
            $ab->toXML($e);
        }

        return $e;
    }
}
