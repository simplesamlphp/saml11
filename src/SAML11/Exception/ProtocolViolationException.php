<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\Exception;

/**
 * This exception may be raised when a violation of the SAML 1.1 specification is detected
 *
 * @package simplesamlphp/saml11
 */
class ProtocolViolationException extends RuntimeException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        if ($message === '') {
            if (defined('static::DEFAULT_MESSAGE')) {
                $message = static::DEFAULT_MESSAGE;
            } else {
                $message = 'A violation of the SAML 1.1 protocol occurred.';
            }
        }

        parent::__construct($message);
    }
}
