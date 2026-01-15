<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\Assert;

use SimpleSAML\Assert\AssertionFailedException;
use SimpleSAML\SAML11\Exception\ProtocolViolationException;

/**
 * @package simplesamlphp/saml11
 */
trait SAMLDateTimeTrait
{
    /**
     */
    protected static function validSAMLDateTime(string $value, string $message = ''): void
    {
        parent::validDateTime($value, $message);

        try {
            /**
             * 1.2.2 Time Values
             *
             * All SAML time values have the type xsd:dateTime, which is built in to the W3C XML Schema Datatypes
             * specification [Schema2], and MUST be expressed in UTC form
             */
            static::endsWith(
                $value,
                'Z',
                $message ?: '%s is not a DateTime expressed in the UTC timezone using the \'Z\' timezone identifier.',
            );
        } catch (AssertionFailedException $e) {
            throw new ProtocolViolationException($e->getMessage());
        }
    }
}
