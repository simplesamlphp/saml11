<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use DateTimeImmutable;
use DOMDocument;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Compat\AbstractContainer;
use SimpleSAML\SAML11\Compat\ContainerSingleton;
use SimpleSAML\SAML11\XML\saml\AbstractAssertionType;
use SimpleSAML\SAML11\XML\saml\AbstractAttributeStatementType;
use SimpleSAML\SAML11\XML\saml\AbstractAuthenticationStatementType;
use SimpleSAML\SAML11\XML\saml\AbstractAuthorizationDecisionStatementType;
use SimpleSAML\SAML11\XML\saml\AbstractSamlElement;
use SimpleSAML\SAML11\XML\saml\AbstractStatement;
use SimpleSAML\SAML11\XML\saml\AbstractStatementType;
use SimpleSAML\SAML11\XML\saml\AbstractSubjectStatement;
use SimpleSAML\SAML11\XML\saml\AbstractSubjectStatementType;
use SimpleSAML\SAML11\XML\saml\Advice;
use SimpleSAML\SAML11\XML\saml\Assertion;
use SimpleSAML\SAML11\XML\saml\AttributeStatement;
use SimpleSAML\SAML11\XML\saml\AuthenticationStatement;
use SimpleSAML\SAML11\XML\saml\AuthorizationDecisionStatement;
use SimpleSAML\SAML11\XML\saml\Conditions;
use SimpleSAML\Test\SAML11\CustomCondition;
use SimpleSAML\Test\SAML11\CustomStatement;
use SimpleSAML\Test\SAML11\CustomSubjectStatement;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;

use function dirname;
use function strval;

/**
 * Tests for Assertion elements.
 *
 * @package simplesamlphp/saml11
 */
#[CoversClass(Assertion::class)]
#[CoversClass(AbstractAssertionType::class)]
#[CoversClass(AbstractSamlElement::class)]
final class AssertionTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;


    /** @var \SimpleSAML\SAML11\Compat\AbstractContainer */
    private static AbstractContainer $containerBackup;

    /** @var \DOMDocument $conditions */
    private static DOMDocument $conditions;

    /** @var \DOMDocument $action */
    //private static DOMDocument $action;

    /** @var \DOMDocument $advice */
    private static DOMDocument $advice;

    /** @var \DOMDocument $statement */
    private static DOMDocument $statement;

    /** @var \DOMDocument $subjectStatement */
    private static DOMDocument $subjectStatement;

    /** @var \DOMDocument $authnStatement */
    private static DOMDocument $authnStatement;

    /** @var \DOMDocument $authzDecisionStatement */
    private static DOMDocument $authzDecisionStatement;

    /** @var \DOMDocument $attributeStatement */
    private static DOMDocument $attributeStatement;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$containerBackup = ContainerSingleton::getInstance();

        self::$schemaFile = dirname(__FILE__, 5) . '/resources/schemas/simplesamlphp.xsd';

        self::$testedClass = Assertion::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Assertion.xml',
        );

        $container = clone self::$containerBackup;
        $container->registerExtensionHandler(CustomCondition::class);
        $container->registerExtensionHandler(CustomStatement::class);
        $container->registerExtensionHandler(CustomSubjectStatement::class);
        ContainerSingleton::setContainer($container);

        self::$conditions = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Conditions.xml',
        );

        self::$advice = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Advice.xml',
        );

        self::$statement = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Statement.xml',
        );

        self::$subjectStatement = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_SubjectStatement.xml',
        );

        self::$authnStatement = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_AuthenticationStatement.xml',
        );

        self::$authzDecisionStatement = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_AuthorizationDecisionStatement.xml',
        );

        self::$attributeStatement = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_AttributeStatement.xml',
        );
    }


    /**
     */
    public static function tearDownAfterClass(): void
    {
        ContainerSingleton::setContainer(self::$containerBackup);
    }


    // marshalling


    /**
     * Test creating an Assertion from scratch
     */
    public function testMarshalling(): void
    {
        $assertion = new Assertion(
            '_abc123',
            'urn:x-simplesamlphp:phpunit',
            new DateTimeImmutable('2023-01-24T09:42:26Z'),
            Conditions::fromXML(self::$conditions->documentElement),
            Advice::fromXML(self::$advice->documentElement),
            [
                AbstractStatement::fromXML(self::$statement->documentElement),
                AbstractSubjectStatement::fromXML(self::$subjectStatement->documentElement),
                AuthenticationStatement::fromXML(self::$authnStatement->documentElement),
                AuthorizationDecisionStatement::fromXML(self::$authzDecisionStatement->documentElement),
                AttributeStatement::fromXML(self::$attributeStatement->documentElement),
            ],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($assertion),
        );
    }


    /**
     * Test getters for diffent types of statements
     */
    public function testStatementGetters(): void
    {
        $assertion = Assertion::fromXML(self::$xmlRepresentation->documentElement);

        $statements = $assertion->getStatements();
        $this->assertContainsOnlyInstancesOf(AbstractStatementType::class, $statements);
        $this->assertCount(1, $statements);

        $subjectStatements = $assertion->getSubjectStatements();
        $this->assertContainsOnlyInstancesOf(AbstractSubjectStatementType::class, $subjectStatements);
        $this->assertCount(1, $subjectStatements);

        $authnStatements = $assertion->getAuthenticationStatements();
        $this->assertContainsOnlyInstancesOf(AbstractAuthenticationStatementType::class, $authnStatements);
        $this->assertCount(1, $authnStatements);

        $authzDecisionStatements = $assertion->getAuthorizationDecisionStatements();
        $this->assertContainsOnlyInstancesOf(
            AbstractAuthorizationDecisionStatementType::class,
            $authzDecisionStatements,
        );
        $this->assertCount(1, $authzDecisionStatements);

        $attrStatements = $assertion->getattributeStatements();
        $this->assertContainsOnlyInstancesOf(AbstractAttributeStatementType::class, $attrStatements);
        $this->assertCount(1, $attrStatements);
    }
}
