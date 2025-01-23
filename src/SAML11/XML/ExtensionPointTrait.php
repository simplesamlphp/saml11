<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML;

use RuntimeException;
use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\XML\Type\{AnyURIValue, NCNameValue, QNameValue};

use function constant;
use function defined;

/**
 * Trait for several extension points objects.
 *
 * @package simplesamlphp/saml11
 */
trait ExtensionPointTrait
{
    /**
     * @return \SimpleSAML\XML\Type\QNameValue
     */
    public function getXsiType(): QNameValue
    {
        return $this->type;
    }


    /**
     * Get the local name for the element's xsi:type.
     *
     * @return \SimpleSAML\XML\Type\NCNameValue
     */
    public static function getXsiTypeName(): NCNameValue
    {
        Assert::true(
            defined('static::XSI_TYPE_NAME'),
            self::getClassName(static::class)
            . '::XSI_TYPE_NAME constant must be defined and set to unprefixed type for the xsi:type it represents.',
            RuntimeException::class,
        );

        return NCNameValue::fromString(constant('static::XSI_TYPE_NAME'));
    }


    /**
     * Get the namespace for the element's xsi:type.
     *
     * @return \SimpleSAML\XML\Type\AnyURIValue
     */
    public static function getXsiTypeNamespaceURI(): AnyURIValue
    {
        Assert::true(
            defined('static::XSI_TYPE_NAMESPACE'),
            self::getClassName(static::class)
            . '::XSI_TYPE_NAMESPACE constant must be defined and set to the namespace for the xsi:type it represents.',
            RuntimeException::class,
        );

        return AnyURIValue::fromString(constant('static::XSI_TYPE_NAMESPACE'));
    }


    /**
     * Get the namespace-prefix for the element's xsi:type.
     *
     * @return \SimpleSAML\XML\Type\NCNameValue
     */
    public static function getXsiTypePrefix(): NCNameValue
    {
        Assert::true(
            defined('static::XSI_TYPE_PREFIX'),
            sprintf(
                '%s::XSI_TYPE_PREFIX constant must be defined and set to the namespace for the xsi:type it represents.',
                self::getClassName(static::class),
            ),
            RuntimeException::class,
        );

        return NCNameValue::fromString(constant('static::XSI_TYPE_PREFIX'));
    }
}
