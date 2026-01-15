<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\Type;

use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\XMLSchema\Type\DateTimeValue;

/**
 * @package simplesaml/saml11
 */
class SAMLDateTimeValue extends DateTimeValue
{
    // Lowercase p as opposed to the base-class to covert the timestamp to UTC as demanded by the SAML specifications
    public const string DATETIME_FORMAT = 'Y-m-d\\TH:i:sp';


    /**
     * Validate the value.
     */
    protected function validateValue(string $value): void
    {
        // Note: value must already be sanitized before validating
        Assert::validSAMLDateTime($this->sanitizeValue($value));
    }
}
