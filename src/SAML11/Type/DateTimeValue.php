<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\Type;

use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\XML\Type\DateTimeValue as BaseDateTimeValue;

/**
 * @package simplesaml/saml11
 */
class DateTimeValue extends BaseDateTimeValue
{
    // Lowercase p as opposed to the base-class to covert the timestamp to UTC as demanded by the SAML specifications
    public const string DATETIME_FORMAT = 'Y-m-d\\TH:i:sp';


    /**
     * Validate the value.
     *
     * @param string $value
     * @return void
     */
    protected function validateValue(string $value): void
    {
        // Note: value must already be sanitized before validating
        Assert::validDateTime($this->sanitizeValue($value));
    }
}
