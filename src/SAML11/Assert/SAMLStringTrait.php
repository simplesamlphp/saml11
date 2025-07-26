<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\Assert;

use SimpleSAML\Assert\AssertionFailedException;
use SimpleSAML\SAML11\Exception\ProtocolViolationException;
use SimpleSAML\XMLSchema\Exception\SchemaViolationException;

/**
 * @package simplesamlphp/saml11
 */
trait SAMLStringTrait
{
    /**
     * @param string $value
     * @param string $message
     */
    protected static function validSAMLString(string $value, string $message = ''): void
    {
        parent::validString($value, $message, SchemaViolationException::class);

        try {
            /**
             * 1.2.1 String and URI Values
             *
             * All strings in SAML messages MUST consist of at least one non-whitespace character
             * (whitespace is defined in the XML Recommendation [XML] §2.3).
             * Empty and whitespace-only values are disallowed.
             */
            static::notWhitespaceOnly($value, $message ?: '%s is not a SAML1.1-compliant string');
        } catch (AssertionFailedException $e) {
            throw new ProtocolViolationException($e->getMessage());
        }
    }
}
