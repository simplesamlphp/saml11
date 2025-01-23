<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};
use SimpleSAML\XML\Type\NCNameValue;
use SimpleSAML\XML\TypedTextContentTrait;

/**
 * Class representing a saml:AssertionIDReference element.
 *
 * @package simplesamlphp/saml11
 */
final class AssertionIDReference extends AbstractSamlElement implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
    use TypedTextContentTrait;

    /** @var string */
    public const TEXTCONTENT_TYPE = NCNameValue::class;
}
