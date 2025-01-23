<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};

/**
 * Class representing a saml:Subject element.
 *
 * @package simplesamlphp/saml11
 */
final class Subject extends AbstractSubjectType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
}
