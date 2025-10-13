<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;

/**
 * Class representing a saml:StatusDetail element.
 *
 * @package simplesamlphp/saml11
 */
final class StatusDetail extends AbstractStatusDetailType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
}
