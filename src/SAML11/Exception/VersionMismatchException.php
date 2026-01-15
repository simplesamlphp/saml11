<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\Exception;

/**
 * This exception may be raised when a message with a wrong version is received.
 *
 * @package simplesamlphp/saml11
 */
class VersionMismatchException extends RuntimeException
{
    /**
     */
    public function __construct(string $message = '')
    {
        if ($message === '') {
            if (defined('static::DEFAULT_MESSAGE')) {
                $message = static::DEFAULT_MESSAGE;
            } else {
                $message = 'A message with the wrong version was received.';
            }
        }

        parent::__construct($message);
    }
}
