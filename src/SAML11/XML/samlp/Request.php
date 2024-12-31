<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DateTimeImmutable;
use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Assert\Assert as SAMLAssert;
use SimpleSAML\SAML11\Exception\ProtocolViolationException;
use SimpleSAML\SAML11\Exception\VersionMismatchException;
use SimpleSAML\SAML11\XML\saml\AssertionIDReference;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\MissingElementException;
use SimpleSAML\XML\Exception\TooManyElementsException;

use function array_merge;
use function array_pop;
use function preg_replace;

/**
 * Class representing a samlp:Request element.
 *
 * @package simplesaml/xml-saml11
 */
final class Request extends AbstractRequestType
{
    /**
     * Convert XML into Request
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
        Assert::same($xml->localName, 'Request', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, Request::NS, InvalidDOMElementException::class);

        $query = AbstractQuery::getChildrenOfClass($xml);
        Assert::maxCount($query, 1, TooManyElementsException::class);

        $subjectQuery = AbstractSubjectQuery::getChildrenOfClass($xml);
        Assert::maxCount($subjectQuery, 1, TooManyElementsException::class);

        $authenticationQuery = AuthenticationQuery::getChildrenOfClass($xml);
        Assert::maxCount($authenticationQuery, 1, TooManyElementsException::class);

        $attributeQuery = AttributeQuery::getChildrenOfClass($xml);
        Assert::maxCount($attributeQuery, 1, TooManyElementsException::class);

        $authorizationDecisionQuery = AuthorizationDecisionQuery::getChildrenOfClass($xml);
        Assert::maxCount($authorizationDecisionQuery, 1, TooManyElementsException::class);

        $query = array_merge(
            $query,
            $subjectQuery,
            $authenticationQuery,
            $attributeQuery,
            $authorizationDecisionQuery,
        );
        Assert::maxCount($query, 1, TooManyElementsException::class);

        $assertionIdReference = AssertionIDReference::getChildrenOfClass($xml);
        $assertionArtifact = AssertionArtifact::getChildrenOfClass($xml);

        Assert::true(
            !empty($query) || !empty($assertionIdReference) || !empty($assertionArtifact),
            TooManyElementsException::class,
        );
        Assert::false(
            empty($query) && empty($assertionIdReference) && empty($assertionArtifact),
            MissingElementException::class,
        );

        $majorVersion = self::getIntegerAttribute($xml, 'MajorVersion');
        Assert::same($majorVersion, 1, VersionMismatchException::class);

        $minorVersion = self::getIntegerAttribute($xml, 'MinorVersion');
        Assert::same($minorVersion, 1, VersionMismatchException::class);

        $issueInstant = self::getAttribute($xml, 'IssueInstant');
        // Strip sub-seconds - See paragraph 1.3.3 of SAML core specifications
        $issueInstant = preg_replace('/([.][0-9]+Z)$/', 'Z', $issueInstant, 1);

        SAMLAssert::validDateTime($issueInstant, ProtocolViolationException::class);
        $issueInstant = new DateTimeImmutable($issueInstant);

        return new static(
            $assertionIdReference ?: $assertionArtifact ?: array_pop($query),
            self::getAttribute($xml, 'RequestID'),
            $majorVersion,
            $minorVersion,
            $issueInstant,
            RespondWith::getChildrenOfClass($xml),
        );
    }
}
