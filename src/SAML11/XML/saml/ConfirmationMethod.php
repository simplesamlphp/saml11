<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use SimpleSAML\SAML11\XML\URIElementTrait;
use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;

/**
 * Class representing a saml:ConfirmationMethod element.
 *
 * @package simplesamlphp/saml11
 */
final class ConfirmationMethod extends AbstractSamlElement implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
    use URIElementTrait;


    /**
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->setContent($content);
    }
}
