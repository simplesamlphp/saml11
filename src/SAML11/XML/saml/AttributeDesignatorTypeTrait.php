<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

/**
 * SAML AttributeDesignator attribute group.
 *
 * @package simplesamlphp/saml11
 */
trait AttributeDesignatorTypeTrait
{
    /**
     * Collect the value of the AttributeName-property
     *
     * @return string
     */
    public function getAttributeName(): string
    {
        return $this->AttributeName;
    }


    /**
     * Collect the value of the AttributeNamespace-property
     *
     * @return string
     */
    public function getAttributeNamespace(): string
    {
        return $this->AttributeNamespace;
    }
}
