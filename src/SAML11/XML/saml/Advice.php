<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};

/**
 * Class representing a saml:Advice element.
 *
 * @package simplesamlphp/saml11
 */
final class Advice extends AbstractAdviceType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
}
