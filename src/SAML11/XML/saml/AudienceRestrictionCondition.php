<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\XML\saml;

use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};

/**
 * Class representing a saml:AudienceRestrictionCondition element.
 *
 * @package simplesamlphp/saml11
 */
final class AudienceRestrictionCondition extends AbstractAudienceRestrictionConditionType implements
    SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;
}
