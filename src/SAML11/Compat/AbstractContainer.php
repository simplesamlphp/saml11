<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\Compat;

use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;
use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\SAML11\XML\ExtensionPointInterface;
use SimpleSAML\XML\AbstractElement;
use SimpleSAML\XML\ElementInterface;
use SimpleSAML\XMLSchema\Type\QNameValue;
use SimpleSAML\XMLSecurity\Alg\Encryption\EncryptionAlgorithmFactory;
use SimpleSAML\XMLSecurity\Alg\KeyTransport\KeyTransportAlgorithmFactory;
use SimpleSAML\XMLSecurity\Alg\Signature\SignatureAlgorithmFactory;

use function array_key_exists;
use function strval;

abstract class AbstractContainer
{
    /** @var array */
    protected array $registry = [];

    /** @var array */
    protected array $extRegistry = [];

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
     * Register a class that can handle a given element.
     *
     * @param class-string $class The class name of a class extending AbstractElement
     */
    public function registerElementHandler(string $class): void
    {
        Assert::subclassOf($class, AbstractElement::class);
        $key = '{' . strval($class::NS) . '}' . AbstractElement::getClassName($class);
        $this->registry[$key] = $class;
    }


    /**
     * Register a class that can handle given extension points of the standard.
     *
     * @param class-string $class
     *   The class name of a class extending AbstractElement or implementing ExtensionPointInterface.
     */
    public function registerExtensionHandler(string $class): void
    {
        Assert::subclassOf($class, ExtensionPointInterface::class);
        $key = '{' . $class::getXsiTypeNamespaceURI() . '}' . $class::getXsiTypeName();
        $this->extRegistry[$key] = $class;
    }


    /**
     * Search for a class that implements an element in the given $namespace.
     *
     * Such classes must have been registered previously by calling registerExtensionHandler(), and they must
     * extend \SimpleSAML\XML\AbstractElement.
     *
     * @param \SimpleSAML\XMLSchema\Type\QNameValue|null $qName The qualified name of the element.
     *
     * @return class-string|null The fully-qualified name of a class extending \SimpleSAML\XML\AbstractElement and
     * implementing support for the given element, or null if no such class has been registered before.
     */
    public function getElementHandler(QNameValue $qName): ?string
    {
        $key = '{' . strval($qName->getNameSpaceURI()) . '}' . strval($qName->getLocalName());
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
     * @param \SimpleSAML\XMLSchema\Type\QNameValue $qName The qualified name of the extension.
     * @return class-string|null The fully-qualified name of a class implementing
     *  \SimpleSAML\SAML11\XML\saml\ExtensionPointInterface or null if no such class has been registered before.
     */
    public function getExtensionHandler(QNameValue $qName): ?string
    {
        $key = '{' . strval($qName->getNameSpaceURI()) . '}' . strval($qName->getLocalName());
        if (array_key_exists($key, $this->extRegistry) === true) {
            Assert::implementsInterface($this->extRegistry[$key], ExtensionPointInterface::class);
            return $this->extRegistry[$key];
        }

        return null;
    }


    /**
     * Get a PSR-3 compatible logger.
     *
     * @return \Psr\Log\LoggerInterface
     */
    abstract public function getLogger(): LoggerInterface;


    /**
     * Get the system clock, using UTC for a timezone
     */
    abstract public function getClock(): ClockInterface;
}
