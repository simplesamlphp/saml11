<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;

/**
 * Class representing a saml:Action element.
 *
 * @package simplesamlphp/saml11
 */
final class Action extends AbstractActionType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
}
