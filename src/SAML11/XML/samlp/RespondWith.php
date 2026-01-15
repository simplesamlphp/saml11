<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use SimpleSAML\XML\TypedTextContentTrait;
use SimpleSAML\XMLSchema\Type\QNameValue;

/**
 * Class representing a samlp:RespondWith element.
 *
 * @package simplesaml/saml11
 */
final class RespondWith extends AbstractSamlpElement
{
    use TypedTextContentTrait;


    public const string TEXTCONTENT_TYPE = QNameValue::class;
}
