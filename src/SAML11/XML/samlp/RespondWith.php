<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use SimpleSAML\XML\Type\QNameValue;
use SimpleSAML\XML\TypedTextContentTrait;

/**
 * Class representing a samlp:RespondWith element.
 *
 * @package simplesaml/saml11
 */
final class RespondWith extends AbstractSamlpElement
{
    use TypedTextContentTrait;

    /** @var string */
    public const TEXTCONTENT_TYPE = QNameValue::class;
}
