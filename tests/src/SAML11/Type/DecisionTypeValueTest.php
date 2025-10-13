<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\Type;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Type\DecisionTypeValue;
use SimpleSAML\SAML11\XML\Enumeration\DecisionTypeEnum;
use SimpleSAML\XMLSchema\Exception\SchemaViolationException;

/**
 * Class \SimpleSAML\Test\SAML11\Type\DecisionTypeValueTest
 *
 * @package simplesamlphp/saml11
 */
#[CoversClass(DecisionTypeValue::class)]
final class DecisionTypeValueTest extends TestCase
{
    /**
     * @param string $decisionType
     * @param bool $shouldPass
     */
    #[DataProvider('provideDecisionType')]
    public function testDecisionTypeValue(string $decisionType, bool $shouldPass): void
    {
        try {
            DecisionTypeValue::fromString($decisionType);
            $this->assertTrue($shouldPass);
        } catch (SchemaViolationException $e) {
            $this->assertFalse($shouldPass);
        }
    }


    /**
     * Test helpers
     */
    public function testHelpers(): void
    {
        $x = DecisionTypeValue::fromEnum(DecisionTypeEnum::Deny);
        $this->assertEquals(DecisionTypeEnum::Deny, $x->toEnum());

        $y = DecisionTypeValue::fromString('Deny');
        $this->assertEquals(DecisionTypeEnum::Deny, $y->toEnum());
    }


    /**
     * @return array<string, array{0: string, 1: boolean}>
     */
    public static function provideDecisionType(): array
    {
        return [
            'deny' => ['Deny', true],
            'indeterminate' => ['Indeterminate', true],
            'permit' => ['Permit', true],
            'undefined' => ['undefined', false],
            'empty' => ['', false],
        ];
    }
}
