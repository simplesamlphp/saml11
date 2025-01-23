<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};

/**
 * Class representing a saml:Conditions element.
 *
 * @package simplesamlphp/saml11
 */
final class Conditions extends AbstractConditionsType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
}
