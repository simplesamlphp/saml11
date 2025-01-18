<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\Type;

use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\XML\Type\StringValue as BaseStringValue;

/**
 * @package simplesaml/saml11
 */
class StringValue extends BaseStringValue
{
    /**
     * Validate the value.
     *
     * @param string $value
     * @return void
     */
    protected function validateValue(string $value): void
    {
        // Note: value must already be sanitized before validating
        Assert::validString($this->sanitizeValue($value));
    }
}
