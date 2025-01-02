<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;
use SimpleSAML\XML\StringElementTrait;

/**
 * SAML Audience element.
 *
 * @package simplesamlphp/saml11
 */
final class Audience extends AbstractSamlElement implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
    use StringElementTrait;


    /**
     * Initialize a saml:Audience from scratch
     *
     * @param string $value
     */
    public function __construct(
        protected string $value,
    ) {
        $this->setContent($value);
    }
}
