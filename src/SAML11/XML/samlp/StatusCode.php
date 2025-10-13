<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;

/**
 * Class representing a saml:StatusCode element.
 *
 * @package simplesamlphp/saml11
 */
final class StatusCode extends AbstractStatusCodeType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
}
