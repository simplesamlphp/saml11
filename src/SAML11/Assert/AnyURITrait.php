<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\Assert;

use SimpleSAML\Assert\AssertionFailedException;
use SimpleSAML\SAML11\Exception\ProtocolViolationException;

/**
 * @package simplesamlphp/saml11
 */
trait AnyURITrait
{
    /**
     * @param string $value
     * @param string $message
     */
    protected static function validAnyURI(string $value, string $message = ''): void
    {
        parent::validAnyURI($value, $message);

        try {
            /**
             * 1.2.1 String and URI Values
             *
             * Unless otherwise indicated in this specification, all URI reference values MUST consist
             * of at least one non-whitespace character
             */
            static::notWhitespaceOnly(
                $value,
                $message ?: '%s is not a SAML1.1-compliant URI',
                ProtocolViolationException::class,
            );
        } catch (AssertionFailedException $e) {
            throw new ProtocolViolationException($e->getMessage());
        }
    }
}
