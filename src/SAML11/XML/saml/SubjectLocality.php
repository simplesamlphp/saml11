<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;

/**
 * Class representing a saml:SubjectLocality element.
 *
 * @package simplesamlphp/saml11
 */
final class SubjectLocality extends AbstractSubjectLocalityType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
}
