<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};

/**
 * Class representing a saml:AuthorityBinding element.
 *
 * @package simplesamlphp/saml11
 */
final class AuthorityBinding extends AbstractAuthorityBindingType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
}
