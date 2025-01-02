<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;

/**
 * Class representing a saml:DoNotCacheCondition element.
 *
 * @package simplesamlphp/saml11
 */
final class DoNotCacheCondition extends AbstractDoNotCacheConditionType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
}
