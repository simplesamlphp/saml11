<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\Assert;

use SimpleSAML\Assert\AssertionFailedException;
use SimpleSAML\SAML11\Exception\ProtocolViolationException;
use SimpleSAML\XML\Exception\SchemaViolationException;

/**
 * @package simplesamlphp/assert
 */
trait CustomAssertionTrait
{
    private static string $scheme_regex = '/^([a-z][a-z0-9\+\-\.]+[:])/i';

    /***********************************************************************************
     *  NOTE:  Custom assertions may be added below this line.                         *
     *         They SHOULD be marked as `protected` to ensure the call is forced       *
     *          through __callStatic().                                                *
     *         Assertions marked `public` are called directly and will                 *
     *          not handle any custom exception passed to it.                          *
     ***********************************************************************************/


    /**
     * @param string $value
     * @param string $message
     */
    protected static function validDateTime(string $value, string $message = ''): void
    {
        parent::validDateTime($value, $message, SchemaViolationException::class);

        try {
            static::endsWith(
                $value,
                'Z',
                $message ?: '%s is not a DateTime expressed in the UTC timezone using the \'Z\' timezone identifier.',
            );
        } catch (AssertionFailedException $e) {
            throw new ProtocolViolationException($e->getMessage());
        }
    }


    /**
     * @param string $value
     * @param string $message
     */
    protected static function validURI(string $value, string $message = ''): void
    {
        parent::validURI($value, $message, SchemaViolationException::class);

        try {
            static::notWhitespaceOnly($value, $message ?: '%s is not a SAML1.1-compliant URI');
            // If it doesn't have a scheme, it's not an absolute URI
            static::regex($value, self::$scheme_regex, $message ?: '%s is not a SAML1.1-compliant URI');
        } catch (AssertionFailedException $e) {
            throw new ProtocolViolationException($e->getMessage());
        }
    }
}
