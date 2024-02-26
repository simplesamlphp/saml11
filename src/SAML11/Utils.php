<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11;

use SimpleSAML\SAML11\Compat\AbstractContainer;
use SimpleSAML\SAML11\Compat\ContainerSingleton;

/**
 * Helper functions for the SAML 1.1 library.
 *
 * @package simplesamlphp/saml11
 */
class Utils
{
    /**
     * @return \SimpleSAML\SAML11\Compat\AbstractContainer
     */
    public static function getContainer(): AbstractContainer
    {
        return ContainerSingleton::getInstance();
    }
}
