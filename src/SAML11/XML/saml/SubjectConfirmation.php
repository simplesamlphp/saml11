<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};

/**
 * Class representing a saml:SubjectConfirmation element.
 *
 * @package simplesamlphp/saml11
 */
final class SubjectConfirmation extends AbstractSubjectConfirmationType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
}
