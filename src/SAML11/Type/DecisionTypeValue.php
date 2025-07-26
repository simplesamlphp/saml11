<?php

declare(strict_types=1);

namespace SimpleSAML\SAML11\Type;

use SimpleSAML\Assert\Assert;
use SimpleSAML\SAML11\XML\saml\DecisionTypeEnum;
use SimpleSAML\XMLSchema\Exception\SchemaViolationException;
use SimpleSAML\XMLSchema\Type\StringValue;

use function array_column;

/**
 * @package simplesamlphp/saml11
 */
class DecisionTypeValue extends StringValue
{
    /** @var string */
    public const SCHEMA_TYPE = 'decisionType';


    /**
     * Validate the value.
     *
     * @param string $value The value
     * @throws \Exception on failure
     * @return void
     */
    protected function validateValue(string $value): void
    {
        Assert::oneOf(
            $this->sanitizeValue($value),
            array_column(DecisionTypeEnum::cases(), 'value'),
            SchemaViolationException::class,
        );
    }


    /**
     * @param \SimpleSAML\SAML11\XML\saml\DecisionTypeEnum $value
     * @return static
     */
    public static function fromEnum(DecisionTypeEnum $value): static
    {
        return new static($value->value);
    }


    /**
     * @return \SimpleSAML\SAML11\XML\saml\DecisionTypeEnum $value
     */
    public function toEnum(): DecisionTypeEnum
    {
        return DecisionTypeEnum::from($this->getValue());
    }
}
