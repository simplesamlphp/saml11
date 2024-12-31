<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use SimpleSAML\XML\QNameElementTrait;

/**
 * Class representing a samlp:RespondWith element.
 *
 * @package simplesaml/xml-saml11
 */
final class RespondWith extends AbstractSamlpElement
{
    use QNameElementTrait;


    /**
     * Initialize a samlp:RespondWith
     *
     * @param string $qname
     * @param string|null $namespaceUri
     */
    public function __construct(string $qname, ?string $namespaceUri = null)
    {
        $this->setContent($qname);
        $this->setContentNamespaceUri($namespaceUri);
    }
}
