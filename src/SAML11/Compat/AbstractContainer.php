<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\Compat;

use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;
use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\SAML11\XML\ExtensionPointInterface;
use SimpleSAML\XML\{AbstractElement, ElementInterface};
use SimpleSAML\XML\Type\QNameValue;
use SimpleSAML\XMLSecurity\Alg\Encryption\EncryptionAlgorithmFactory;
use SimpleSAML\XMLSecurity\Alg\KeyTransport\KeyTransportAlgorithmFactory;
use SimpleSAML\XMLSecurity\Alg\Signature\SignatureAlgorithmFactory;

use function array_key_exists;
use function implode;
use function is_subclass_of;

abstract class AbstractContainer
{
    /** @var array */
    protected array $registry = [];

    /** @var array|null */
    protected ?array $blacklistedEncryptionAlgorithms = [
        EncryptionAlgorithmFactory::DEFAULT_BLACKLIST,
        KeyTransportAlgorithmFactory::DEFAULT_BLACKLIST,
        SignatureAlgorithmFactory::DEFAULT_BLACKLIST,
    ];


    /**
     * Get the list of algorithms that are blacklisted for any encryption operation.
     *
     * @return string[]|null An array with all algorithm identifiers that are blacklisted, or null if we want to use the
     * defaults.
     */
    public function getBlacklistedEncryptionAlgorithms(): ?array
    {
        return $this->blacklistedEncryptionAlgorithms;
    }


    /**
     * Register a class that can handle given extension points of the standard.
     *
     * @param string $class The class name of a class extending AbstractElement or implementing ExtensionPointInterface.
     * @psalm-param class-string $class
     */
    public function registerExtensionHandler(string $class): void
    {
        Assert::subclassOf($class, AbstractElement::class);
        if (is_subclass_of($class, ExtensionPointInterface::class, true)) {
            $key = '{' . $class::getXsiTypeNamespaceURI() . '}' . $class::getXsiTypePrefix() . ':' . $class::getXsiTypeName();
        } else {
            $className = AbstractElement::getClassName($class);
            $key = ($class::NS === null) ? $className : implode(':', [$class::NS, $className]);
        }
        $this->registry[$key] = $class;
    }


    /**
     * Search for a class that implements an $element in the given $namespace.
     *
     * Such classes must have been registered previously by calling registerExtensionHandler(), and they must
     * extend \SimpleSAML\XML\AbstractElement.
     *
     * @param \SimpleSAML\XML\Typr\QNameValue|null $qName The qualified name of the element.
     *
     * @return string|null The fully-qualified name of a class extending \SimpleSAML\XML\AbstractElement and
     * implementing support for the given element, or null if no such class has been registered before.
     * @psalm-return class-string|null
     */
    public function getElementHandler(QNameValue $qName): ?string
    {
        $key = $qName->getRawValue();
        if (array_key_exists($key, $this->registry) === true) {
            Assert::implementsInterface($this->registry[$key], ElementInterface::class);
            return $this->registry[$key];
        }

        return null;
    }


    /**
     * Search for a class that implements a custom element type.
     *
     * Such classes must have been registered previously by calling registerExtensionHandler(), and they must
     * implement \SimpleSAML\SAML11\XML\saml\ExtensionPointInterface.
     *
     * @param \SimpleSAML\XML\Type\QNameValue $qName The qualified name of the extension.
     * @return string|null The fully-qualified name of a class implementing
     *  \SimpleSAML\SAML11\XML\saml\ExtensionPointInterface or null if no such class has been registered before.
     * @psalm-return class-string|null
     */
    public function getExtensionHandler(QNameValue $qName): ?string
    {
        $prefix = $qName->getNamespacePrefix()->getValue();
        $namespaceURI = $qName->getNamespaceURI()->getValue();

        if ($namespaceURI !== null) {
            $key = $qName->getRawValue();
            if (array_key_exists($key, $this->registry) === true) {
                Assert::implementsInterface($this->registry[$key], ExtensionPointInterface::class);
                return $this->registry[$key];
            }
            return null;
        }

        return null;
    }


    /**
     * Get a PSR-3 compatible logger.
     * @return \Psr\Log\LoggerInterface
     */
    abstract public function getLogger(): LoggerInterface;


    /**
     * Get the system clock, using UTC for a timezone
     */
    abstract public function getClock(): ClockInterface;
}
