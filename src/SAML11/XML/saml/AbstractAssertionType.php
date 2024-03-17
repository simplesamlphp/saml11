<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DateTimeImmutable;
use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Compat\ContainerSingleton;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Exception\ProtocolViolationException;
use SimpleSAML\SAML11\Utils\XPath;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\MissingElementException;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\Exception\TooManyElementsException;
use SimpleSAML\XMLSecurity\XML\ds\Signature;
use SimpleSAML\XMLSecurity\XML\SignableElementInterface;
use SimpleSAML\XMLSecurity\XML\SignableElementTrait;
use SimpleSAML\XMLSecurity\XML\SignedElementInterface;
use SimpleSAML\XMLSecurity\XML\SignedElementTrait;

use function array_filter;
use function array_merge;
use function array_pop;
use function array_values;
use function preg_replace;

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
     * @param string $assertionID
     * @param string $issuer
     * @param \DateTimeImmutable $issueInstant
     * @param \SimpleSAML\SAML11\XML\saml\Conditions|null $conditions
     * @param \SimpleSAML\SAML11\XML\saml\Advice|null $advice
     * @param array<\SimpleSAML\SAML11\XML\saml\AbstractStatementType> $statements
     */
    final public function __construct(
        protected string $assertionID,
        protected string $issuer,
        protected DateTimeImmutable $issueInstant,
        protected ?Conditions $conditions = null,
        protected ?Advice $advice = null,
        protected array $statements = [],
    ) {
        Assert::same($issueInstant->getTimeZone()->getName(), 'Z', ProtocolViolationException::class);
        Assert::validNCName($assertionID, SchemaViolationException::class);
        Assert::minCount($statements, 1, MissingElementException::class);
        Assert::maxCount($statements, C::UNBOUNDED_LIMIT);
        Assert::allIsInstanceOf($statements, AbstractStatementType::class, SchemaViolationException::class);
    }


    /**
     * Collect the value of the assertionID-property
     *
     * Note: the name of this method is not consistent, but it has to be named getId for xml-security to work.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->assertionID;
    }


    /**
     * Collect the value of the issuer-property
     *
     * @return string
     */
    public function getIssuer(): string
    {
        return $this->issuer;
    }


    /**
     * Collect the value of the issueInstant-property
     *
     * @return \DateTimeImmutable
     */
    public function getIssueInstant(): DateTimeImmutable
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

        Assert::same(self::getIntegerAttribute($xml, 'MajorVersion'), 1, 'Unsupported major version: %s');
        Assert::same(self::getIntegerAttribute($xml, 'MinorVersion'), 1, 'Unsupported minor version: %s');

        $assertionID = self::getAttribute($xml, 'AssertionID');
        Assert::validNCName($assertionID); // Covers the empty string

        $issueInstant = self::getAttribute($xml, 'IssueInstant');
        // Strip sub-seconds - See paragraph 1.2.2 of SAML core specifications
        $issueInstant = preg_replace('/([.][0-9]+Z)$/', 'Z', $issueInstant, 1);

        Assert::validDateTimeZulu($issueInstant, ProtocolViolationException::class);
        $issueInstant = new DateTimeImmutable($issueInstant);

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
            $assertionID,
            self::getAttribute($xml, 'Issuer'),
            $issueInstant,
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

        $e->setAttribute('MajorVersion', '1');
        $e->setAttribute('MinorVersion', '1');
        $e->setAttribute('AssertionID', $this->getId());
        $e->setAttribute('Issuer', $this->getIssuer());
        $e->setAttribute('IssueInstant', $this->getIssueInstant()->format(C::DATETIME_FORMAT));

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
    public function toXML(DOMElement $parent = null): DOMElement
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
