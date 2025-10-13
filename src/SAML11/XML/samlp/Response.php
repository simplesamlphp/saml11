<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\SAML11\Exception\VersionMismatchException;
use SimpleSAML\SAML11\Type\SAMLAnyURIValue;
use SimpleSAML\SAML11\Type\SAMLDateTimeValue;
use SimpleSAML\SAML11\XML\saml\Assertion;
use SimpleSAML\SAML11\XML\samlp\Status;
use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSchema\Exception\MissingElementException;
use SimpleSAML\XMLSchema\Exception\TooManyElementsException;
use SimpleSAML\XMLSchema\Type\IDValue;
use SimpleSAML\XMLSchema\Type\NCNameValue;
use SimpleSAML\XMLSchema\Type\NonNegativeIntegerValue;

use function array_pop;

/**
 * Class representing a samlp:Response element.
 *
 * @package simplesaml/saml11
 */
final class Response extends AbstractResponseType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;


    /**
     * Convert XML into Response
     *
     * @param \DOMElement $xml The XML element we should load
     * @return static
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     * @throws \SimpleSAML\XML\Exception\TooManyElementsException
     *   if too many child-elements of a type are specified
     * @throws \SimpleSAML\XML\Exception\MissingElementException
     *   if one of the mandatory child-elements is missing
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'Response', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, Response::NS, InvalidDOMElementException::class);

        $majorVersion = self::getAttribute($xml, 'MajorVersion', NonNegativeIntegerValue::class);
        Assert::same($majorVersion->getValue(), '1', VersionMismatchException::class);

        $minorVersion = self::getAttribute($xml, 'MinorVersion', NonNegativeIntegerValue::class);
        Assert::same($minorVersion->getValue(), '1', VersionMismatchException::class);

        $status = Status::getChildrenOfClass($xml);
        Assert::minCount($status, 1, MissingElementException::class);
        Assert::maxCount($status, 1, TooManyElementsException::class);

        return new static(
            $majorVersion,
            $minorVersion,
            self::getAttribute($xml, 'ResponseID', IDValue::class),
            array_pop($status),
            self::getAttribute($xml, 'IssueInstant', SAMLDateTimeValue::class),
            Assertion::getChildrenOfClass($xml),
            self::getOptionalAttribute($xml, 'InResponseTo', NCNameValue::class, null),
            self::getOptionalAttribute($xml, 'Recipient', SAMLAnyURIValue::class, null),
        );
    }
}
