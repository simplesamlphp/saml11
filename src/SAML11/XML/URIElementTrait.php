<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML;

use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Exception\ProtocolViolationException;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\URIElementTrait as BaseURIElementTrait;

/**
 * Trait extending the default URIElementTrait to comply with the restrictions added by the SAML 1.1 specifications.
 *
 * @package simplesamlphp/saml11
 */
trait URIElementTrait
{
    use BaseURIElementTrait;

    /**
     * Validate the content of the element.
     *
     * @param string $content  The value to go in the XML textContent
     * @throws \Exception on failure
     * @return void
     */
    protected function validateContent(string $content): void
    {
        /**
         * 1.2.1 String and URI Values
         *
         * All SAML string and URI reference values have the types xsd:string and xsd:anyURI respectively, which
         * are built in to the W3C XML Schema Datatypes specification [Schema2]. All strings in SAML messages
         * MUST consist of at least one non-whitespace character (whitespace is defined in the XML
         * Recommendation [XML] §2.3). Empty and whitespace-only values are disallowed. Also, unless otherwise
         * indicated in this specification, all URI reference values MUST consist of at least one non-whitespace
         * character, and are strongly RECOMMENDED to be absolute [RFC 2396].
         */
        Assert::notWhitespaceOnly($content, ProtocolViolationException::class);
        Assert::validURI($content, SchemaViolationException::class);
    }
}
