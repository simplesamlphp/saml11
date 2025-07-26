<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\Assert;

use SimpleSAML\XML\Assert\Assert as BaseAssert;

/**
 * SimpleSAML\SAML11\Assert\Assert wrapper class
 *
 * @package simplesamlphp/saml11
 *
 * @method static void validSAMLAnyURI(mixed $value, string $message = '', string $exception = '')
 * @method static void validSAMLDateTime(mixed $value, string $message = '', string $exception = '')
 * @method static void validSAMLString(mixed $value, string $message = '', string $exception = '')
 * @method static void nullOrValidSAMLAnyURI(mixed $value, string $message = '', string $exception = '')
 * @method static void nullOrValidSAMLDateTime(mixed $value, string $message = '', string $exception = '')
 * @method static void nullOrValidSAMLString(mixed $value, string $message = '', string $exception = '')
 * @method static void allValidSAMLAnyURI(mixed $value, string $message = '', string $exception = '')
 * @method static void allValidSAMLDateTime(mixed $value, string $message = '', string $exception = '')
 * @method static void allValidSAMLString(mixed $value, string $message = '', string $exception = '')
 */
class Assert extends BaseAssert
{
    use SAMLAnyURITrait;
    use SAMLDateTimeTrait;
    use SAMLStringTrait;
}
