<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Exception\{ProtocolViolationException, VersionMismatchException};
use SimpleSAML\SAML11\Type\SAMLDateTimeValue;
use SimpleSAML\SAML11\XML\saml\AssertionIDReference;
use SimpleSAML\XMLSchema\Exception\{InvalidDOMElementException, MissingElementException, TooManyElementsException};
use SimpleSAML\XMLSchema\Type\{IDValue, NonNegativeIntegerValue};

use function array_merge;
use function array_pop;

/**
 * Class representing a samlp:Request element.
 *
 * @package simplesaml/saml11
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

        $majorVersion = self::getAttribute($xml, 'MajorVersion', NonNegativeIntegerValue::class);
        Assert::same($majorVersion->getValue(), '1', VersionMismatchException::class);

        $minorVersion = self::getAttribute($xml, 'MinorVersion', NonNegativeIntegerValue::class);
        Assert::same($minorVersion->getValue(), '1', VersionMismatchException::class);

        $issueInstant = self::getAttribute($xml, 'IssueInstant', SAMLDateTimeValue::class);

        return new static(
            $assertionIdReference ?: $assertionArtifact ?: array_pop($query),
            self::getAttribute($xml, 'RequestID', IDValue::class),
            $majorVersion,
            $minorVersion,
            $issueInstant,
            RespondWith::getChildrenOfClass($xml),
        );
    }
}
