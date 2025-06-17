<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};

/**
 * Class representing a saml:Evidence element.
 *
 * @package simplesamlphp/saml11
 */
final class Evidence extends AbstractEvidenceType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
}
