<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};

/**
 * Class representing a saml:AttributeStatement element.
 *
 * @package simplesamlphp/saml11
 */
final class AttributeStatement extends AbstractAttributeStatementType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
}
