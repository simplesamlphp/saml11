<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Utils;
use SimpleSAML\SAML11\XML\ExtensionPointInterface;
use SimpleSAML\SAML11\XML\ExtensionPointTrait;
use SimpleSAML\XML\Attribute as XMLAttribute;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;
use SimpleSAML\XMLSchema\Constants as C_XSI;
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSchema\Exception\SchemaViolationException;
use SimpleSAML\XMLSchema\Type\QNameValue;

/**
 * SAMLP Query data type.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractQuery extends AbstractQueryAbstractType implements
    ExtensionPointInterface,
    SchemaValidatableElementInterface
{
    use ExtensionPointTrait;
    use SchemaValidatableElementTrait;


    /** @var string */
    public const LOCALNAME = 'Query';


    /**
     * Initialize a custom samlp:Query element.
     *
     * @param \SimpleSAML\XMLSchema\Type\QNameValue $type
     */
    protected function __construct(
        protected QNameValue $type,
    ) {
    }


    /**
     * Convert an XML element into a Query.
     *
     * @param \DOMElement $xml The root XML element
     * @return static
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'Query', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, C::NS_SAMLP, InvalidDOMElementException::class);
        Assert::true(
            $xml->hasAttributeNS(C_XSI::NS_XSI, 'type'),
            'Missing required xsi:type in <samlp:Query> element.',
            SchemaViolationException::class,
        );

        $type = QNameValue::fromDocument($xml->getAttributeNS(C_XSI::NS_XSI, 'type'), $xml);

        // now check if we have a handler registered for it
        $handler = Utils::getContainer()->getExtensionHandler($type);
        if ($handler === null) {
            // we don't have a handler, proceed with unknown query
            return new UnknownQuery(new Chunk($xml), $type);
        }

        Assert::subclassOf(
            $handler,
            AbstractQuery::class,
            'Elements implementing Query must extend \SimpleSAML\SAML11\XML\samlp\AbstractQuery.',
        );
        return $handler::fromXML($xml);
    }


    /**
     * Convert this Query to XML.
     *
     * @param \DOMElement $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this Query.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        if (!$e->lookupPrefix($this->getXsiType()->getNamespaceURI()->getValue())) {
            $e->setAttributeNS(
                'http://www.w3.org/2000/xmlns/',
                'xmlns:' . static::getXsiTypePrefix()->getValue(),
                static::getXsiTypeNamespaceURI()->getValue(),
            );
        }

        $type = new XMLAttribute(C_XSI::NS_XSI, 'xsi', 'type', $this->getXsiType());
        $type->toXML($e);

        return $e;
    }
}
