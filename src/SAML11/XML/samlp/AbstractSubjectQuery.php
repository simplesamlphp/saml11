<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\SAML11\Assert\Assert;
use SimpleSAML\SAML11\Constants as C;
use SimpleSAML\SAML11\Utils;
use SimpleSAML\SAML11\XML\{ExtensionPointInterface, ExtensionPointTrait};
use SimpleSAML\SAML11\XML\saml\Subject;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};
use SimpleSAML\XMLSchema\Constants as C_XSI;
use SimpleSAML\XMLSchema\Exception\{
    InvalidDOMElementException,
    MissingElementException,
    SchemaViolationException,
    TooManyElementsException,
};
use SimpleSAML\XMLSchema\Type\QNameValue;

use function array_pop;

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
     * @param \SimpleSAML\XMLSchema\Type\QNameValue $type
     */
    protected function __construct(
        protected QNameValue $type,
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
            $xml->hasAttributeNS(C_XSI::NS_XSI, 'type'),
            'Missing required xsi:type in <samlp:SubjectQuery> element.',
            SchemaViolationException::class,
        );

        $type = QNameValue::fromDocument($xml->getAttributeNS(C_XSI::NS_XSI, 'type'), $xml);

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
}
