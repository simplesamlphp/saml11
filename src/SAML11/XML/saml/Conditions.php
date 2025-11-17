<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;

/**
 * Class representing a saml:Conditions element.
 *
 * @package simplesamlphp/saml11
 */
final class Conditions extends AbstractConditionsType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;

    /**
     * This element doesn't allow arbitrary namespace-declarations and therefore cannot be normalized
     * @var bool
     */
    final public const NORMALIZATION = false;
}
