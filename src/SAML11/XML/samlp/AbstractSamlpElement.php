<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\XML\AbstractElement;

/**
 * Abstract class to be implemented by all the classes in this namespace
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractSamlpElement extends AbstractElement
{
    /** @var string */
    public const NS = C::NS_SAMLP;

    /** @var string */
    public const NS_PREFIX = 'samlp';

    /** @var string */
    public const SCHEMA = 'resources/schemas/oasis-sstc-saml-schema-protocol-1.1.xsd';
}
