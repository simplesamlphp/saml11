<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Type\AnyURIValue;
use SimpleSAML\SAML11\XML\saml\{Action, Evidence, Subject};
use SimpleSAML\XML\Exception\{InvalidDOMElementException, MissingElementException, TooManyElementsException};
use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};

use function array_pop;

/**
 * Class representing a samlp:AuthorizationDecisionQuery element.
 *
 * @package simplesaml/saml11
 */
final class AuthorizationDecisionQuery extends AbstractAuthorizationDecisionQueryType implements
    SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;

    /**
     * Convert XML into a AuthorizationDecisionQuery
     *
     * @param \DOMElement $xml The XML element we should load
     * @return static
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   if the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): static
    {
        Assert::same($xml->localName, 'AuthorizationDecisionQuery', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, AuthorizationDecisionQuery::NS, InvalidDOMElementException::class);

        $resource = self::getAttribute($xml, 'Resource', AnyURIValue::class);

        $subject = Subject::getChildrenOfClass($xml);
        Assert::minCount($subject, 1, MissingElementException::class);
        Assert::maxCount($subject, 1, TooManyElementsException::class);

        $action = Action::getChildrenOfClass($xml);
        Assert::minCount($action, 1, MissingElementException::class);

        $evidence = Evidence::getChildrenOfClass($xml);
        Assert::maxCount($evidence, 1, TooManyElementsException::class);

        return new static(array_pop($subject), $resource, array_pop($evidence), $action);
    }
}
