<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\XML\Exception\InvalidDOMElementException;

/**
 * SAML StatusCode data type.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractStatusCodeType extends AbstractSamlpElement
{
    /**
     * Initialize a samlp:StatusCode
     *
     * @param string $Value
     * @param \SimpleSAML\SAML11\XML\samlp\StatusCode[] $subCodes
     */
    final public function __construct(
        protected string $Value = C::STATUS_SUCCESS,
        protected array $subCodes = [],
    ) {
        Assert::validQName($Value);
        Assert::maxCount($subCodes, C::UNBOUNDED_LIMIT);
        Assert::allIsInstanceOf($subCodes, StatusCode::class);
    }


    /**
     * Collect the Value
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->Value;
    }


    /**
     * Collect the subcodes
     *
     * @return \SimpleSAML\SAML11\XML\samlp\StatusCode[]
     */
    public function getSubCodes(): array
    {
        return $this->subCodes;
    }


    /**
     * Convert XML into a StatusCode
     *
     * @param \DOMElement $xml The XML element we should load
     * @return static
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     * @throws \SimpleSAML\XML\Exception\MissingAttributeException
     *   if the supplied element is missing one of the mandatory attributes
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'StatusCode', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, StatusCode::NS, InvalidDOMElementException::class);

        $Value = self::getAttribute($xml, 'Value');
        $subCodes = StatusCode::getChildrenOfClass($xml);

        return new static(
            $Value,
            $subCodes,
        );
    }


    /**
     * Convert this StatusCode to XML.
     *
     * @param \DOMElement|null $parent The element we should append this StatusCode to.
     * @return \DOMElement
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);
        $e->setAttribute('Value', $this->getValue());

        foreach ($this->getSubCodes() as $subCode) {
            $subCode->toXML($e);
        }

        return $e;
    }
}
