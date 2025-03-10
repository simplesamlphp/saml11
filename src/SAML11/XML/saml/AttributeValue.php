<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DateTimeImmutable;
use DateTimeInterface;
use DOMElement;
use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\XML\AbstractElement;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;

use function class_exists;
use function explode;
use function gettype;
use function intval;
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
     * The value of this element. Can be one of:
     *  - string
     *  - int
     *  - \SimpleSAML\XML\AbstractElement
     *
     * @param string|int|\DateTimeInterface|\SimpleSAML\XML\AbstractElement $value
     * @throws \SimpleSAML\Assert\AssertionFailedException if the supplied value is neither a string or a DOMElement
     */
    final public function __construct(
        protected string|int|DateTimeInterface|AbstractElement $value,
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
        $type = gettype($value);

        switch ($type) {
            case "integer":
                return "xs:integer";
            case "object":
                if ($value instanceof DateTimeInterface) {
                    return 'xs:dateTime';
                }

                return sprintf(
                    '%s:%s',
                    $this->value::getNamespacePrefix(),
                    AbstractElement::getClassName(get_class($value)),
                );
            default:
                return "xs:string";
        }
    }


    /**
     * Get this attribute value.
     *
     * @return string|int|\SimpleSAML\XML\AbstractElement[]|null
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
            Assert::numeric($xml->textContent);

            // we have an integer as value
            $value = intval($xml->textContent);
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
            Assert::validDateTime($xml->textContent);

            // we have a dateTime as value
            $value = new DateTimeImmutable($xml->textContent);
        } else {
            $value = $xml->textContent;
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
            case "integer":
                // make sure that the xs namespace is available in the AttributeValue
                $e->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', C::NS_XSI);
                $e->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xs', C::NS_XS);
                $e->setAttributeNS(C::NS_XSI, 'xsi:type', 'xs:integer');
                $e->textContent = strval($value);
                break;
            case "object":
                if ($value instanceof DateTimeInterface) {
                    $e->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', C::NS_XSI);
                    $e->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xs', C::NS_XS);
                    $e->setAttributeNS(C::NS_XSI, 'xsi:type', 'xs:dateTime');
                    $e->textContent = $value->format(C::DATETIME_FORMAT);
                } else {
                    $value->toXML($e);
                }
                break;
            default: // string
                $e->textContent = $value;
                break;
        }

        return $e;
    }
}
