<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Type\{DateTimeValue, StringValue};
use SimpleSAML\XML\AbstractElement;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};
use SimpleSAML\XML\Type\{IntegerValue, ValueTypeInterface};

use function class_exists;
use function explode;
use function gettype;
use function str_contains;
use function strval;

/**
 * Serializable class representing an AttributeValue.
 *
 * @package simplesamlphp/saml11
 */
class AttributeValue extends AbstractSamlElement implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;

    /**
     * Create an AttributeValue.
     *
     * @param \SimpleSAML\XML\Type\ValueTypeInterface|\SimpleSAML\XML\AbstractElement $value
     * @throws \SimpleSAML\Assert\AssertionFailedException if the supplied value is neither a string or a DOMElement
     */
    final public function __construct(
        protected StringValue|IntegerValue|DateTimeValue|AbstractElement $value,
    ) {
    }


    /**
     * Get the XSI type of this attribute value.
     *
     * @return string
     */
    public function getXsiType(): string
    {
        $value = $this->getValue();

        if ($value === null) {
            return 'xs:nil';
        } elseif ($value instanceof ValueTypeInterface) {
            return $value::SCHEMA_NAMESPACE_PREFIX . ':' . $value::SCHEMA_TYPE;
        } else {
            return sprintf(
                '%s:%s',
                $value::getNamespacePrefix(),
                $value::getLocalName(),
            );
        }
    }


    /**
     * Get this attribute value.
     *
     * @return (
     *   \SimpleSAML\XML\Type\IntegerValue|
     *   \SimpleSAML\SAML11\Type\StringValue|
     *   \SimpleSAML\SAML11\Type\DateTimeValue|
     *   \SimpleSAML\XML\AbstractElement|
     *   null
     * )
     */
    public function getValue()
    {
        return $this->value;
    }


    /**
     * Convert XML into a AttributeValue
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

        if ($xml->childElementCount > 0) {
            $node = $xml->firstElementChild;

            if (str_contains($node->tagName, ':')) {
                list($prefix, $eltName) = explode(':', $node->tagName);
                $className = sprintf('\SimpleSAML\SAML11\XML\%s\%s', $prefix, $eltName);

                if (class_exists($className)) {
                    $value = $className::fromXML($node);
                } else {
                    $value = Chunk::fromXML($node);
                }
            } else {
                $value = Chunk::fromXML($node);
            }
        } elseif (
            $xml->hasAttributeNS(C::NS_XSI, "type") &&
            $xml->getAttributeNS(C::NS_XSI, "type") === "xs:integer"
        ) {
            // we have an integer as value
            $value = IntegerValue::fromString($xml->textContent);
        } elseif (
            $xml->hasAttributeNS(C::NS_XSI, "nil") &&
            ($xml->getAttributeNS(C::NS_XSI, "nil") === "1" || $xml->getAttributeNS(C::NS_XSI, "nil") === "true")
        ) {
            // we have a nill value
            $value = null;
        } elseif (
            $xml->hasAttributeNS(C::NS_XSI, "type") &&
            $xml->getAttributeNS(C::NS_XSI, "type") === "xs:dateTime"
        ) {
            // we have a dateTime as value
            $value = DateTimeValue::fromString($xml->textContent);
        } else {
            $value = StringValue::fromString($xml->textContent);
        }

        return new static($value);
    }


    /**
     * Append this attribute value to an element.
     *
     * @param \DOMElement|null $parent The element we should append this attribute value to.
     *
     * @return \DOMElement The generated AttributeValue element.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = parent::instantiateParentElement($parent);

        $value = $this->getValue();
        $type = gettype($value);

        switch ($type) {
            case IntegerValue::class:
                // make sure that the xs namespace is available in the AttributeValue
                $e->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', C::NS_XSI);
                $e->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xs', C::NS_XS);
                $e->setAttributeNS(C::NS_XSI, 'xsi:type', 'xs:integer');
                $e->textContent = strval($value);
                break;
            case "object":
                if ($value instanceof DateTimeValue) {
                    $e->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', C::NS_XSI);
                    $e->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xs', C::NS_XS);
                    $e->setAttributeNS(C::NS_XSI, 'xsi:type', 'xs:dateTime');
                    $e->textContent = strval($value);
                } elseif ($value instanceof ValueTypeInterface) {
                    if ($value instanceof IntegerValue) {
                        $e->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', C::NS_XSI);
                        $e->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xs', C::NS_XS);
                        $e->setAttributeNS(C::NS_XSI, 'xsi:type', 'xs:integer');
                    }
                    $e->textContent = strval($value);
                } else {
                    $value->toXML($e);
                }
                break;
            default: // string
                $e->textContent = strval($value);
                break;
        }

        return $e;
    }
}
