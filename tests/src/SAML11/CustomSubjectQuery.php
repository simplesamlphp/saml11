<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\XML\saml\Subject;
use SimpleSAML\SAML11\XML\samlp\{AbstractSubjectQuery, StatusMessage};
use SimpleSAML\XML\Attribute as XMLAttribute;
use SimpleSAML\XMLSchema\Constants as C_XSI;
use SimpleSAML\XMLSchema\Exception\{InvalidDOMElementException, MissingElementException, TooManyElementsException};
use SimpleSAML\XMLSchema\Type\QNameValue;

use function array_pop;

/**
 * Example class to demonstrate how SubjectQuery can be extended.
 *
 * @package simplesamlphp\saml11
 */
final class CustomSubjectQuery extends AbstractSubjectQuery
{
    /** @var string */
    protected const XSI_TYPE_NAME = 'CustomSubjectQueryType';

    /** @var string */
    protected const XSI_TYPE_NAMESPACE = 'urn:x-simplesamlphp:namespace';

    /** @var string */
    protected const XSI_TYPE_PREFIX = 'ssp';


    /**
     * CustomSubjectQuery constructor.
     *
     * @param \SimpleSAML\SAML11\XML\samlp\StatusMessage[] $statusMessage
     */
    public function __construct(
        Subject $subject,
        protected array $statusMessage,
    ) {
        Assert::allIsInstanceOf($statusMessage, StatusMessage::class);

        parent::__construct(
            QNameValue::fromString(
                '{' . self::XSI_TYPE_NAMESPACE . '}' . self::XSI_TYPE_PREFIX . ':' . self::XSI_TYPE_NAME,
            ),
            $subject,
        );
    }


    /**
     * Get the value of the statusMessage-attribute.
     *
     * @return \SimpleSAML\SAML11\XML\samlp\StatusMessage[]
     */
    public function getStatusMessage(): array
    {
        return $this->statusMessage;
    }


    /**
     * Convert XML into a Query
     *
     * @param \DOMElement $xml The XML element we should load
     * @return static
     *
     * @throws \SimpleSAML\XMLSchema\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'SubjectQuery', InvalidDOMElementException::class);
        Assert::notNull($xml->namespaceURI, InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, AbstractSubjectQuery::NS, InvalidDOMElementException::class);
        Assert::true(
            $xml->hasAttributeNS(C_XSI::NS_XSI, 'type'),
            'Missing required xsi:type in <samlp:SubjectQuery> element.',
            InvalidDOMElementException::class,
        );

        $type = QNameValue::fromDocument($xml->getAttributeNS(C_XSI::NS_XSI, 'type'), $xml);
        Assert::same($type->getValue(), self::XSI_TYPE_PREFIX . ':' . self::XSI_TYPE_NAME);

        $statusMessage = StatusMessage::getChildrenOfClass($xml);

        $subject = Subject::getChildrenOfClass($xml);
        Assert::minCount($subject, 1, MissingElementException::class);
        Assert::maxCount($subject, 1, TooManyElementsException::class);

        return new static(array_pop($subject), $statusMessage);
    }


    /**
     * Convert this SubjectQuery to XML.
     *
     * @param \DOMElement $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this SubjectQuery.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        if (!$e->lookupPrefix($this->getXsiType()->getNamespaceURI()->getValue())) {
            $namespace = new XMLAttribute(
                'http://www.w3.org/2000/xmlns/',
                'xmlns',
                $this->getXsiType()->getNamespacePrefix()->getValue(),
                $this->getXsiType()->getNamespaceURI(),
            );
            $namespace->toXML($e);
        }

        if (!$e->lookupPrefix('xsi')) {
            $type = new XMLAttribute(C_XSI::NS_XSI, 'xsi', 'type', $this->getXsiType());
            $type->toXML($e);
        }

        $this->getSubject()->toXML($e);

        foreach ($this->getStatusMessage() as $statusMessage) {
            $statusMessage->toXML($e);
        }

        return $e;
    }
}
