<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Utils;
use SimpleSAML\SAML11\XML\ExtensionPointInterface;
use SimpleSAML\SAML11\XML\ExtensionPointTrait;
use SimpleSAML\XML\Attribute as XMLAttribute;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XMLSchema\Constants as C_XSI;
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSchema\Exception\MissingElementException;
use SimpleSAML\XMLSchema\Exception\SchemaViolationException;
use SimpleSAML\XMLSchema\Exception\TooManyElementsException;
use SimpleSAML\XMLSchema\Type\QNameValue;

/**
 * Class implementing the <saml:SubjectStatement> extension point.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractSubjectStatement extends AbstractSubjectStatementType implements ExtensionPointInterface
{
    use ExtensionPointTrait;


    public const string LOCALNAME = 'SubjectStatement';


    /**
     * Initialize a custom saml:SubjectStatement element.
     *
     * @param \SimpleSAML\XMLSchema\Type\QNameValue $type
     */
    protected function __construct(
        protected QNameValue $type,
        Subject $subject,
    ) {
        parent::__construct($subject);
    }


    /**
     * @return \SimpleSAML\XMLSchema\Type\QNameValue
     */
    public function getXsiType(): QNameValue
    {
        return $this->type;
    }


    /**
     * Convert an XML element into a SubjectStatement.
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'SubjectStatement', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, C::NS_SAML, InvalidDOMElementException::class);
        Assert::true(
            $xml->hasAttributeNS(C_XSI::NS_XSI, 'type'),
            'Missing required xsi:type in <saml:SubjectStatement> element.',
            SchemaViolationException::class,
        );

        $type = QNameValue::fromDocument($xml->getAttributeNS(C_XSI::NS_XSI, 'type'), $xml);

        // now check if we have a handler registered for it
        $handler = Utils::getContainer()->getExtensionHandler($type);
        if ($handler === null) {
            $subject = Subject::getChildrenOfClass($xml);
            Assert::minCount($subject, 1, MissingElementException::class);
            Assert::maxCount($subject, 1, TooManyElementsException::class);

            // we don't have a handler, proceed with unknown SubjectStatement
            return new UnknownSubjectStatement(new Chunk($xml), $type, array_pop($subject));
        }

        Assert::subclassOf(
            $handler,
            AbstractSubjectStatement::class,
            sprintf('Elements implementing SubjectStatement must extend \%s.', AbstractSubjectStatementType::class),
        );
        return $handler::fromXML($xml);
    }


    /**
     * Convert this SubjectStatement to XML.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = parent::toXML($parent);

        if (!$e->lookupPrefix($this->getXsiType()->getNamespaceURI()->getValue())) {
            $e->setAttributeNS(
                'http://www.w3.org/2000/xmlns/',
                'xmlns:' . static::getXsiTypePrefix(),
                static::getXsiTypeNamespaceURI()->getValue(),
            );
        }

        $type = new XMLAttribute(C_XSI::NS_XSI, 'xsi', 'type', $this->getXsiType());
        $type->toXML($e);

        return $e;
    }
}
