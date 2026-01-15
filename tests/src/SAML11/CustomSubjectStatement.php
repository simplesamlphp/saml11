<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\XML\saml\AbstractSubjectStatement;
use SimpleSAML\SAML11\XML\saml\Audience;
use SimpleSAML\SAML11\XML\saml\Subject;
use SimpleSAML\XMLSchema\Constants as C_XSI;
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSchema\Exception\MissingElementException;
use SimpleSAML\XMLSchema\Exception\TooManyElementsException;
use SimpleSAML\XMLSchema\Type\QNameValue;

/**
 * Example class to demonstrate how SubjectStatement can be extended.
 *
 * @package simplesamlphp\saml11
 */
final class CustomSubjectStatement extends AbstractSubjectStatement
{
    protected const string XSI_TYPE_NAME = 'CustomSubjectStatementType';

    protected const string XSI_TYPE_NAMESPACE = 'urn:x-simplesamlphp:namespace';

    protected const string XSI_TYPE_PREFIX = 'ssp';


    /**
     * CustomSubjectStatement constructor.
     *
     * @param \SimpleSAML\SAML11\XML\saml\Subject $subject
     * @param \SimpleSAML\SAML11\XML\saml\Audience[] $audience
     */
    public function __construct(
        Subject $subject,
        protected array $audience,
    ) {
        Assert::allIsInstanceOf($audience, Audience::class);

        parent::__construct(
            QNameValue::fromString(
                '{' . self::XSI_TYPE_NAMESPACE . '}' . self::XSI_TYPE_PREFIX . ':' . self::XSI_TYPE_NAME,
            ),
            $subject,
        );
    }


    /**
     * Get the value of the audience-attribute.
     *
     * @return \SimpleSAML\SAML11\XML\saml\Audience[]
     */
    public function getAudience(): array
    {
        return $this->audience;
    }


    /**
     * Convert XML into an SubjectStatement
     *
     * @throws \SimpleSAML\XMLSchema\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'SubjectStatement', InvalidDOMElementException::class);
        Assert::notNull($xml->namespaceURI, InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, AbstractSubjectStatement::NS, InvalidDOMElementException::class);
        Assert::true(
            $xml->hasAttributeNS(C_XSI::NS_XSI, 'type'),
            'Missing required xsi:type in <saml:SubjectStatement> element.',
            InvalidDOMElementException::class,
        );

        $type = $xml->getAttributeNS(C_XSI::NS_XSI, 'type');
        Assert::same($type, self::XSI_TYPE_PREFIX . ':' . self::XSI_TYPE_NAME);

        $subject = Subject::getChildrenOfClass($xml);
        Assert::minCount($subject, 1, MissingElementException::class);
        Assert::maxCount($subject, 1, TooManyElementsException::class);

        $audience = Audience::getChildrenOfClass($xml);

        return new static(array_pop($subject), $audience);
    }


    /**
     * Convert this SubjectStatement to XML.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = parent::toXML($parent);

        foreach ($this->audience as $audience) {
            $audience->toXML($e);
        }

        return $e;
    }
}
