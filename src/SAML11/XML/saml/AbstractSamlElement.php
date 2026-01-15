<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\XML\AbstractElement;

/**
 * Abstract class to be implemented by all the classes in this namespace
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractSamlElement extends AbstractElement
{
    public const string NS = C::NS_SAML;

    public const string NS_PREFIX = 'saml';

    public const string SCHEMA = 'resources/schemas/oasis-sstc-saml-schema-assertion-1.1.xsd';
}
