<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use SimpleSAML\SAML11\XML\URIElementTrait;
use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;

/**
 * SAML Audience element.
 *
 * @package simplesamlphp/saml11
 */
final class Audience extends AbstractSamlElement implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
    use URIElementTrait;


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
