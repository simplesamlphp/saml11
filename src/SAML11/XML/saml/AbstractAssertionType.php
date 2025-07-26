<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\SAML11\Compat\ContainerSingleton;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Exception\VersionMismatchException;
use SimpleSAML\SAML11\Utils\XPath;
use SimpleSAML\SAML11\Type\{SAMLDateTimeValue, SAMLStringValue};
use SimpleSAML\XMLSchema\Exception\{
    InvalidDOMElementException,
    MissingElementException,
    SchemaViolationException,
    TooManyElementsException,
};
use SimpleSAML\XMLSchema\Type\{IDValue, NonNegativeIntegerValue};
use SimpleSAML\XMLSecurity\XML\ds\Signature;
use SimpleSAML\XMLSecurity\XML\{SignableElementInterface, SignableElementTrait};
use SimpleSAML\XMLSecurity\XML\{SignedElementInterface, SignedElementTrait};

use function array_filter;
use function array_merge;
use function array_pop;
use function array_values;
use function strval;

/**
 * SAML Assertion Type abstract data type.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractAssertionType extends AbstractSamlElement implements
    SignableElementInterface,
    SignedElementInterface
{
    use SignableElementTrait;
    use SignedElementTrait;


    /**
     * The original signed XML
     *
     * @var \DOMElement
     */
    protected DOMElement $xml;


    /**
     * Initialize a saml:AssertionType from scratch
     *
     * @param \SimpleSAML\XMLSchema\Type\NonNegativeIntegerValue $majorVersion
     * @param \SimpleSAML\XMLSchema\Type\NonNegativeIntegerValue $minorVersion
     * @param \SimpleSAML\XMLSchema\Type\IDValue $assertionID
     * @param \SimpleSAML\SAML11\Type\SAMLStringValue $issuer
     * @param \SimpleSAML\SAML11\Type\SAMLDateTimeValue $issueInstant
     * @param \SimpleSAML\SAML11\XML\saml\Conditions|null $conditions
     * @param \SimpleSAML\SAML11\XML\saml\Advice|null $advice
     * @param array<\SimpleSAML\SAML11\XML\saml\AbstractStatementType> $statements
     */
    final public function __construct(
        protected NonNegativeIntegerValue $majorVersion,
        protected NonNegativeIntegerValue $minorVersion,
        protected IDValue $assertionID,
        protected SAMLStringValue $issuer,
        protected SAMLDateTimeValue $issueInstant,
        protected ?Conditions $conditions = null,
        protected ?Advice $advice = null,
        protected array $statements = [],
    ) {
        Assert::same($majorVersion->getValue(), '1', VersionMismatchException::class);
        Assert::same($minorVersion->getValue(), '1', VersionMismatchException::class);
        Assert::minCount($statements, 1, MissingElementException::class);
        Assert::maxCount($statements, C::UNBOUNDED_LIMIT);
        Assert::allIsInstanceOf($statements, AbstractStatementType::class, SchemaViolationException::class);
    }


    /**
     * Collect the value of the majorVersion-property
     *
     * @return \SimpleSAML\XMLSchema\Type\NonNegativeIntegerValue
     */
    public function getMajorVersion(): NonNegativeIntegerValue
    {
        return $this->majorVersion;
    }


    /**
     * Collect the value of the minorVersion-property
     *
     * @return \SimpleSAML\XMLSchema\Type\NonNegativeIntegerValue
     */
    public function getMinorVersion(): NonNegativeIntegerValue
    {
        return $this->minorVersion;
    }


    /**
     * Collect the value of the assertionID-property
     *
     * Note: the name of this method is not consistent, but it has to be named getId for xml-security to work.
     *
     * @return \SimpleSAML\XMLSchema\Type\IDValue
     */
    public function getId(): IDValue
    {
        return $this->assertionID;
    }


    /**
     * Collect the value of the issuer-property
     *
     * @return \SimpleSAML\SAML11\Type\SAMLStringValue
     */
    public function getIssuer(): SAMLStringValue
    {
        return $this->issuer;
    }


    /**
     * Collect the value of the issueInstant-property
     *
     * @return \SimpleSAML\SAML11\Type\SAMLDateTimeValue
     */
    public function getIssueInstant(): SAMLDateTimeValue
    {
        return $this->issueInstant;
    }


    /**
     * Collect the value of the conditions-property
     *
     * @return \SimpleSAML\SAML11\XML\saml\Conditions|null
     */
    public function getConditions(): ?Conditions
    {
        return $this->conditions;
    }


    /**
     * Collect the value of the advice-property
     *
     * @return \SimpleSAML\SAML11\XML\saml\Advice|null
     */
    public function getAdvice(): ?Advice
    {
        return $this->advice;
    }


    /**
     * Collect the value of the statements-property
     *
     * @return array<\SimpleSAML\SAML11\XML\saml\AbstractStatementType>
     */
    public function getAllStatements(): array
    {
        return $this->statements;
    }


    /**
     * @return \SimpleSAML\SAML11\XML\saml\AbstractStatement[]
     */
    public function getStatements(): array
    {
        return array_values(array_filter($this->statements, function ($statement) {
            return $statement instanceof AbstractStatement;
        }));
    }


    /**
     * @return \SimpleSAML\SAML11\XML\saml\AbstractSubjectStatement[]
     */
    public function getSubjectStatements(): array
    {
        return array_values(array_filter($this->statements, function ($statement) {
            return $statement instanceof AbstractSubjectStatement;
        }));
    }


    /**
     * @return \SimpleSAML\SAML11\XML\saml\AuthenticationStatement[]
     */
    public function getAuthenticationStatements(): array
    {
        return array_values(array_filter($this->statements, function ($statement) {
            return $statement instanceof AuthenticationStatement;
        }));
    }


    /**
     * @return \SimpleSAML\SAML11\XML\saml\AuthorizationDecisionStatement[]
     */
    public function getAuthorizationDecisionStatements(): array
    {
        return array_values(array_filter($this->statements, function ($statement) {
            return $statement instanceof AuthorizationDecisionStatement;
        }));
    }


    /**
     * @return \SimpleSAML\SAML11\XML\saml\AttributeStatement[]
     */
    public function getAttributeStatements(): array
    {
        return array_values(array_filter($this->statements, function ($statement) {
            return $statement instanceof AttributeStatement;
        }));
    }


    /**
     * Set the XML element.
     *
     * @param \DOMElement $xml
     */
    private function setOriginalXML(DOMElement $xml): void
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


    public function getBlacklistedAlgorithms(): ?array
    {
        $container = ContainerSingleton::getInstance();
        return $container->getBlacklistedEncryptionAlgorithms();
    }


    /**
     * Convert XML into an AssertionType
     *
     * @param \DOMElement $xml The XML element we should load
     * @return static
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, static::getLocalName(), InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, static::NS, InvalidDOMElementException::class);

        $conditions = Conditions::getChildrenOfClass($xml);
        Assert::maxCount(
            $conditions,
            1,
            'More than one <saml:Conditions> in <saml:Assertion>.',
            TooManyElementsException::class,
        );

        $advice = Advice::getChildrenOfClass($xml);
        Assert::maxCount(
            $advice,
            1,
            'More than one <saml:Advice> in <saml:Assertion>.',
            TooManyElementsException::class,
        );

        $statements = AbstractStatement::getChildrenOfClass($xml);
        $subjectStatement = AbstractSubjectStatement::getChildrenOfClass($xml);
        $authnStatement = AuthenticationStatement::getChildrenOfClass($xml);
        $authzDecisionStatement = AuthorizationDecisionStatement::getChildrenOfClass($xml);
        $attrStatement = AttributeStatement::getChildrenOfClass($xml);

        $signature = Signature::getChildrenOfClass($xml);
        Assert::maxCount($signature, 1, 'Only one <ds:Signature> element is allowed.', TooManyElementsException::class);

        $assertion = new static(
            self::getAttribute($xml, 'MajorVersion', NonNegativeIntegerValue::class),
            self::getAttribute($xml, 'MinorVersion', NonNegativeIntegerValue::class),
            self::getAttribute($xml, 'AssertionID', IDValue::class),
            self::getAttribute($xml, 'Issuer', SAMLStringValue::class),
            self::getAttribute($xml, 'IssueInstant', SAMLDateTimeValue::class),
            array_pop($conditions),
            array_pop($advice),
            array_merge($statements, $subjectStatement, $authnStatement, $authzDecisionStatement, $attrStatement),
        );

        if (!empty($signature)) {
            $assertion->setSignature($signature[0]);
            $assertion->setOriginalXML($xml);
        }

        return $assertion;
    }


    /**
     * Convert this assertion to an unsigned XML document.
     * This method does not sign the resulting XML document.
     *
     * @return \DOMElement The root element of the DOM tree
     */
    protected function toUnsignedXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        $e->setAttribute('MajorVersion', strval($this->getMajorVersion()));
        $e->setAttribute('MinorVersion', strval($this->getMinorVersion()));
        $e->setAttribute('AssertionID', strval($this->getId()));
        $e->setAttribute('Issuer', strval($this->getIssuer()));
        $e->setAttribute('IssueInstant', strval($this->getIssueInstant()));

        $this->getConditions()?->toXML($e);
        $this->getAdvice()?->toXML($e);

        foreach ($this->getAllStatements() as $statement) {
            $statement->toXML($e);
        }

        return $e;
    }


    /**
     * Convert this assertion to a signed XML element, if a signer was set.
     *
     * @param \DOMElement|null $parent The DOM node the assertion should be created in.
     *
     * @return \DOMElement This assertion.
     * @throws \Exception
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        if ($this->isSigned() === true && $this->signer === null) {
            // We already have a signed document and no signer was set to re-sign it
            if ($parent === null) {
                return $this->getOriginalXML();
            }

            $node = $parent->ownerDocument?->importNode($this->getOriginalXML(), true);
            $parent->appendChild($node);
            return $parent;
        }

        $e = $this->toUnsignedXML($parent);

        if ($this->signer !== null) {
            $signedXML = $this->doSign($e);

            // Test for last element, if any
            $assertionElements = XPath::xpQuery(
                $signedXML,
                './saml_assertion/following-sibling::*[position() = last()]',
                XPath::getXPath($signedXML),
            );
            $last = array_pop($assertionElements);

            if ($last !== null) {
                $signedXML->insertBefore($this->signature?->toXML($signedXML), $last->nextSibling);
            } else {
                $signedXML->appendChild($this->signature?->toXML($signedXML));
            }

            return $signedXML;
        }

        return $e;
    }
}
