<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};

/**
 * Class representing a saml:Attribute element.
 *
 * @package simplesamlphp/saml11
 */
final class Attribute extends AbstractAttributeType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
}
