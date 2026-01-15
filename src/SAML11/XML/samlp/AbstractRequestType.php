<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\samlp;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\Type\SAMLDateTimeValue;
use SimpleSAML\SAML11\XML\saml\AssertionIDReference;
use SimpleSAML\XMLSchema\Exception\SchemaViolationException;
use SimpleSAML\XMLSchema\Type\IDValue;
use SimpleSAML\XMLSchema\Type\NonNegativeIntegerValue;

use function array_pop;
use function is_array;

/**
 * Base class for all SAML 1.1 requests.
 *
 * @package simplesamlphp/saml11
 */
abstract class AbstractRequestType extends AbstractRequestAbstractType
{
    /**
     * Initialize a request.
     *
     * @param (
     *   \SimpleSAML\SAML11\XML\samlp\AbstractQueryAbstractType|
     *   array<\SimpleSAML\SAML11\XML\saml\AssertionIDReference>|
     *   array<\SimpleSAML\SAML11\XML\samlp\AssertionArtifact>
     * ) $request
     * @param \SimpleSAML\XMLSchema\Type\IDValue $id
     * @param \SimpleSAML\XMLSchema\Type\NonNegativeIntegerValue $majorVersion
     * @param \SimpleSAML\XMLSchema\Type\NonNegativeIntegerValue $minorVersion
     * @param \SimpleSAML\SAML11\Type\SAMLDateTimeValue $issueInstant
     * @param array<\SimpleSAML\SAML11\XML\samlp\RespondWith> $respondWith
     *
     * @throws \Exception
     */
    protected function __construct(
        protected AbstractQueryAbstractType|array $request,
        IDValue $id,
        NonNegativeIntegerValue $majorVersion,
        NonNegativeIntegerValue $minorVersion,
        SAMLDateTimeValue $issueInstant,
        array $respondWith = [],
    ) {
        if (is_array($request)) {
            Assert::minCount($request, 1, SchemaViolationException::class);

            $req = array_pop($request);
            if ($req instanceof AssertionIDReference) {
                Assert::allIsInstanceOf($request, AssertionIDReference::class, SchemaViolationException::class);
            } elseif ($req instanceof AssertionArtifact) {
                Assert::allIsInstanceOf($request, AssertionArtifact::class, SchemaViolationException::class);
            } else {
                throw new SchemaViolationException();
            }
        }

        parent::__construct($id, $majorVersion, $minorVersion, $issueInstant, $respondWith);
    }


    /**
     * Retrieve the request inside this request.
     *
     * @return (
     *   \SimpleSAML\SAML11\XML\samlp\AbstractQueryAbstractType|
     *   array<\SimpleSAML\SAML11\XML\saml\AssertionIDReference>|
     *   array<\SimpleSAML\SAML11\XML\samlp\AssertionArtifact>
     * )
     */
    public function getRequest(): AbstractQueryAbstractType|array
    {
        return $this->request;
    }


    /**
     * Convert this message to an unsigned XML document.
     * This method does not sign the resulting XML document.
     */
    protected function toUnsignedXML(?DOMElement $parent = null): DOMElement
    {
        $e = parent::toUnsignedXML($parent);

        $requests = $this->getRequest();
        $requests = is_array($requests) ? $requests : [$requests];

        foreach ($requests as $request) {
            $request->toXML($e);
        }

        return $e;
    }
}
