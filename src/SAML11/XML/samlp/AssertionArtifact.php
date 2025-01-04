<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use SimpleSAML\SAML11\XML\StringElementTrait;
use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;

/**
 * SAML AssertionArtifact element.
 *
 * @package simplesamlphp/saml11
 */

final class AssertionArtifact extends AbstractSamlpElement implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
    use StringElementTrait;


    /**
     * Initialize a saml:AssertionArtifact from scratch
     *
     * @param string $value
     */
    public function __construct(
        protected string $value,
    ) {
        $this->setContent($value);
    }
}
