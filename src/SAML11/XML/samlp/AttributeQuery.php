<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Type\SAMLAnyURIValue;
use SimpleSAML\SAML11\XML\saml\AttributeDesignator;
use SimpleSAML\SAML11\XML\saml\Subject;
use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSchema\Exception\MissingElementException;
use SimpleSAML\XMLSchema\Exception\TooManyElementsException;

use function array_pop;

/**
 * Class representing a samlp:AttributeQuery element.
 *
 * @package simplesaml/saml11
 */
final class AttributeQuery extends AbstractAttributeQueryType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;


    /**
     * Convert XML into a AttributeQuery
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'AttributeQuery', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, AttributeQuery::NS, InvalidDOMElementException::class);

        $resource = self::getOptionalAttribute($xml, 'Resource', SAMLAnyURIValue::class, null);

        $subject = Subject::getChildrenOfClass($xml);
        Assert::minCount($subject, 1, MissingElementException::class);
        Assert::maxCount($subject, 1, TooManyElementsException::class);

        $attributeDesignator = AttributeDesignator::getChildrenOfClass($xml);

        return new static(array_pop($subject), $resource, $attributeDesignator);
    }
}
