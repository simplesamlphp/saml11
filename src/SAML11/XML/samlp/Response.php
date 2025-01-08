<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DateTimeImmutable;
use DOMElement;
use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\SAML11\Assert\Assert as SAMLAssert;
use SimpleSAML\SAML11\Exception\ProtocolViolationException;
use SimpleSAML\SAML11\Exception\VersionMismatchException;
use SimpleSAML\SAML11\XML\saml\Assertion;
use SimpleSAML\SAML11\XML\samlp\Status;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\MissingElementException;
use SimpleSAML\XML\Exception\TooManyElementsException;
use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;

use function array_pop;

/**
 * Class representing a samlp:Response element.
 *
 * @package simplesaml/xml-saml11
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

        $majorVersion = self::getIntegerAttribute($xml, 'MajorVersion');
        Assert::same($majorVersion, 1, VersionMismatchException::class);

        $minorVersion = self::getIntegerAttribute($xml, 'MinorVersion');
        Assert::same($minorVersion, 1, VersionMismatchException::class);

        $issueInstant = self::getAttribute($xml, 'IssueInstant');
        // Strip sub-seconds - See paragraph 1.3.3 of SAML core specifications
        $issueInstant = preg_replace('/([.][0-9]+Z)$/', 'Z', $issueInstant, 1);

        SAMLAssert::validDateTime($issueInstant, ProtocolViolationException::class);
        $issueInstant = new DateTimeImmutable($issueInstant);

        $status = Status::getChildrenOfClass($xml);
        Assert::minCount($status, 1, MissingElementException::class);
        Assert::maxCount($status, 1, TooManyElementsException::class);

        return new static(
            self::getAttribute($xml, 'ResponseID'),
            array_pop($status),
            Assertion::getChildrenOfClass($xml),
            $majorVersion,
            $minorVersion,
            $issueInstant,
            $inResponseTo = self::getOptionalAttribute($xml, 'InResponseTo', null),
            $recipient = self::getOptionalAttribute($xml, 'Recipient', null),
        );
    }
}
