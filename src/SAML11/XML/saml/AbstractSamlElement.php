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
    /** @var string */
    public const NS = C::NS_SAML;

    /** @var string */
    public const NS_PREFIX = 'saml';

    /** @var string */
    public const SCHEMA = 'resources/schemas/oasis-sstc-saml-schema-assertion-1.1.xsd';
}
