<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use SimpleSAML\XML\StringElementTrait;

/**
 * SAML AssertionArtifact element.
 *
 * @package simplesamlphp/saml11
 */

final class AssertionArtifact extends AbstractSamlpElement
{
    use StringElementTrait;


    /**
     * Initialize a saml:AssertionArtifac from scratch
     *
     * @param string $value
     */
    public function __construct(
        protected string $value,
    ) {
        $this->setContent($value);
    }
}