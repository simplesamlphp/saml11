<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\Assert;

use SimpleSAML\XML\Assert\Assert as BaseAssert;

/**
 * SimpleSAML\SAML11\Assert\Assert wrapper class
 *
 * @package simplesamlphp/saml11
 *
 * @method static void validDateTime(mixed $value, string $message = '', string $exception = '')
 * @method static void validEntityID(mixed $value, string $message = '', string $exception = '')
 * @method static void validURI(mixed $value, string $message = '', string $exception = '')
 * @method static void nullOrValidDateTime(mixed $value, string $message = '', string $exception = '')
 * @method static void nullOrValidEntityID(mixed $value, string $message = '', string $exception = '')
 * @method static void nullOrValidURI(mixed $value, string $message = '', string $exception = '')
 * @method static void allValidDateTime(mixed $value, string $message = '', string $exception = '')
 * @method static void allValidEntityID(mixed $value, string $message = '', string $exception = '')
 * @method static void allValidURI(mixed $value, string $message = '', string $exception = '')
 */
class Assert extends BaseAssert
{
    use AnyURITrait;
    use DateTimeTrait;
    use StringTrait;
}
