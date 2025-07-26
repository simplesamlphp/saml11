<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Type\SAMLStringValue;
use SimpleSAML\XML\AbstractElement;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};
use SimpleSAML\XMLSchema\Constants as C_XSI;
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSchema\Type\IntegerValue;
use SimpleSAML\XMLSchema\Type\Interface\ValueTypeInterface;

use function class_exists;
use function explode;
use function gettype;
use function str_contains;
use function strval;

/**
 * Serializable class representing an SubjectConfirmationData.
 *
 * @package simplesamlphp/saml11
 */
class SubjectConfirmationData extends AbstractSamlElement implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;

    /**
     * Create an SubjectConfirmationData.
     *
     * @param mixed $value The value of this element. Can be one of:
     *  - \SimpleSAML\XMLSchema\Type\IntegerValue
     *  - \SimpleSAML\SAML11\Type\SAMLStringValue
     *  - null
     *  - \SimpleSAML\XML\AbstractElement
     *
     * @throws \SimpleSAML\Assert\AssertionFailedException if the supplied value is neither a string or a DOMElement
     */
    final public function __construct(
        protected SAMLStringValue|IntegerValue|null|AbstractElement $value,
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
     *   \SimpleSAML\XMLSchema\Type\IntegerValue|
     *   \SimpleSAML\SAML11\Type\SAMLStringValue|
     *   \SimpleSAML\XML\AbstractElement|
     *   null
     * )
     */
    public function getValue(): SAMLStringValue|IntegerValue|AbstractElement|null
    {
        return $this->value;
    }


    /**
     * Convert XML into a SubjectConfirmationData
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
            $xml->hasAttributeNS(C_XSI::NS_XSI, "type") &&
            $xml->getAttributeNS(C_XSI::NS_XSI, "type") === "xs:integer"
        ) {
            // we have an integer as value
            $value = IntegerValue::fromString($xml->textContent);
        } elseif (
            // null value
            $xml->hasAttributeNS(C_XSI::NS_XSI, "nil") &&
            ($xml->getAttributeNS(C_XSI::NS_XSI, "nil") === "1" ||
                $xml->getAttributeNS(C_XSI::NS_XSI, "nil") === "true")
        ) {
            $value = null;
        } else {
            $value = SAMLStringValue::fromString($xml->textContent);
        }

        return new static($value);
    }


    /**
     * Append this attribute value to an element.
     *
     * @param \DOMElement|null $parent The element we should append this attribute value to.
     *
     * @return \DOMElement The generated SubjectConfirmationData element.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = parent::instantiateParentElement($parent);

        $value = $this->getValue();
        $type = gettype($value);

        switch ($type) {
            case "integer":
                // make sure that the xs namespace is available in the SubjectConfirmationData
                $e->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', C_XSI::NS_XSI);
                $e->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xs', C_XSI::NS_XS);
                $e->setAttributeNS(C_XSI::NS_XSI, 'xsi:type', 'xs:integer');
                $e->textContent = strval($value);
                break;
            case "NULL":
                $e->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', C_XSI::NS_XSI);
                $e->setAttributeNS(C_XSI::NS_XSI, 'xsi:nil', '1');
                $e->textContent = '';
                break;
            case "object":
                if ($value instanceof ValueTypeInterface) {
                    if ($this->value instanceof IntegerValue) {
                        $e->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', C_XSI::NS_XSI);
                        $e->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xs', C_XSI::NS_XS);
                        $e->setAttributeNS(C_XSI::NS_XSI, 'xsi:type', 'xs:integer');
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
