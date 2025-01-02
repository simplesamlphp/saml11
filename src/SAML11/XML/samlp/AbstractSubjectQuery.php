<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Utils;
use SimpleSAML\SAML11\XML\ExtensionPointInterface;
use SimpleSAML\SAML11\XML\ExtensionPointTrait;
use SimpleSAML\SAML11\XML\saml\Subject;
use SimpleSAML\XML\Attribute as XMLAttribute;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\MissingElementException;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\Exception\TooManyElementsException;
use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;

use function array_pop;
use function count;
use function explode;

/**
 * SAMLP Query data type.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractSubjectQuery extends AbstractSubjectQueryAbstractType implements
    ExtensionPointInterface,
    SchemaValidatableElementInterface
{
    use ExtensionPointTrait;
    use SchemaValidatableElementTrait;

    /** @var string */
    public const LOCALNAME = 'SubjectQuery';


    /**
     * Initialize a custom samlp:SubjectQuery element.
     *
     * @param string $type
     */
    protected function __construct(
        protected string $type,
        Subject $subject,
    ) {
        parent::__construct($subject);
    }


    /**
     * Convert an XML element into a SubjectQuery.
     *
     * @param \DOMElement $xml The root XML element
     * @return static
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'SubjectQuery', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, C::NS_SAMLP, InvalidDOMElementException::class);
        Assert::true(
            $xml->hasAttributeNS(C::NS_XSI, 'type'),
            'Missing required xsi:type in <samlp:SubjectQuery> element.',
            SchemaViolationException::class,
        );

        $type = $xml->getAttributeNS(C::NS_XSI, 'type');
        Assert::validQName($type, SchemaViolationException::class);

        // first, try to resolve the type to a full namespaced version
        $qname = explode(':', $type, 2);
        if (count($qname) === 2) {
            list($prefix, $element) = $qname;
        } else {
            $prefix = null;
            list($element) = $qname;
        }
        $ns = $xml->lookupNamespaceUri($prefix);
        $type = ($ns === null) ? $element : implode(':', [$ns, $element]);

        // now check if we have a handler registered for it
        $handler = Utils::getContainer()->getExtensionHandler($type);
        if ($handler === null) {
            $subject = Subject::getChildrenOfClass($xml);
            Assert::minCount($subject, 1, MissingElementException::class);
            Assert::maxCount($subject, 1, TooManyElementsException::class);

            // we don't have a handler, proceed with unknown query
            return new UnknownSubjectQuery(new Chunk($xml), $type, array_pop($subject));
        }

        Assert::subclassOf(
            $handler,
            AbstractSubjectQuery::class,
            'Elements implementing SubjectQuery must extend \SimpleSAML\SAML11\XML\samlp\AbstractSubjectQuery.',
        );

        return $handler::fromXML($xml);
    }


    /**
     * Convert this SubjectQuery to XML.
     *
     * @param \DOMElement $parent The element we are converting to XML.
     * @return \DOMElement The XML element after adding the data corresponding to this SubjectQuery.
     */
    public function toXML(?DOMElement $parent = null): DOMElement
    {
        // This unfortunately doesn't work because namespace attributes get messed up
        //$e = parent::toXML($parent);

        $e = $this->instantiateParentElement($parent);
        $e->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:' . static::getXsiTypePrefix(),
            static::getXsiTypeNamespaceURI(),
        );

        $type = new XMLAttribute(C::NS_XSI, 'xsi', 'type', $this->getXsiType());
        $type->toXML($e);

        $this->getSubject()->toXML($e);

        return $e;
    }
}
