<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};

/**
 * Class representing a samlp:Status element.
 *
 * @package simplesaml/saml11
 */
final class Status extends AbstractStatusType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
}
