<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use SimpleSAML\SAML11\Type\SAMLAnyURIValue;
use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;
use SimpleSAML\XML\TypedTextContentTrait;

/**
 * SAML Audience element.
 *
 * @package simplesamlphp/saml11
 */
final class Audience extends AbstractSamlElement implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
    use TypedTextContentTrait;


    public const string TEXTCONTENT_TYPE = SAMLAnyURIValue::class;
}
