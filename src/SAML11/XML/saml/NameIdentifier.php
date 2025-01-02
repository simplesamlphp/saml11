<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;

/**
 * Class representing a saml:NameIdentifier element.
 *
 * @package simplesamlphp/saml11
 */
final class NameIdentifier extends AbstractNameIdentifierType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
}
