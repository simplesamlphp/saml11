<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use DOMDocument;
use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Compat\{AbstractContainer, ContainerSingleton};
use SimpleSAML\SAML11\Type\SAMLAnyURIValue;
use SimpleSAML\SAML11\XML\saml\{
    AbstractAuthorizationDecisionStatementType,
    AbstractSamlElement,
    Action,
    AuthorizationDecisionStatement,
    DecisionTypeEnum,
    Evidence,
    Subject,
};
use SimpleSAML\Test\SAML11\{CustomCondition, CustomStatement, CustomSubjectStatement};
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\{SchemaValidationTestTrait, SerializableElementTestTrait};

use function dirname;
use function strval;

/**
 * Tests for AuthorizationDecisionStatement elements.
 *
 * @package simplesamlphp/saml11
 */
#[Group('saml')]
#[CoversClass(AuthorizationDecisionStatement::class)]
#[CoversClass(AbstractAuthorizationDecisionStatementType::class)]
#[CoversClass(AbstractSamlElement::class)]
final class AuthorizationDecisionStatementTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /** @var \SimpleSAML\SAML11\Compat\AbstractContainer */
    private static AbstractContainer $containerBackup;

    /** @var \DOMDocument $action */
    private static DOMDocument $action;

    /** @var \DOMDocument $advice */
//    private static DOMDocument $advice;

    /** @var \DOMDocument $evidence */
    private static DOMDocument $evidence;

    /** @var \DOMDocument $subject */
    private static DOMDocument $subject;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$containerBackup = ContainerSingleton::getInstance();

        self::$schemaFile = dirname(__FILE__, 5) . '/resources/schemas/simplesamlphp.xsd';

        self::$testedClass = AuthorizationDecisionStatement::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_AuthorizationDecisionStatement.xml',
        );

        $container = clone self::$containerBackup;
        $container->registerExtensionHandler(CustomCondition::class);
        $container->registerExtensionHandler(CustomStatement::class);
        $container->registerExtensionHandler(CustomSubjectStatement::class);
        ContainerSingleton::setContainer($container);

        self::$action = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Action.xml',
        );

//        self::$advice = DOMDocumentFactory::fromFile(
//            dirname(__FILE__, 5) . '/resources/xml/saml_Advice.xml',
//        );

        self::$evidence = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Evidence.xml',
        );

        self::$subject = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Subject.xml',
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
     * Test creating an AuthorizationDecisionStatement from scratch.
     */
    public function testMarshalling(): void
    {
        $subject = Subject::fromXML(self::$subject->documentElement);
        $action = Action::fromXML(self::$action->documentElement);
        $evidence = Evidence::fromXML(self::$evidence->documentElement);

        $authzDecisionStatement = new AuthorizationDecisionStatement(
            $subject,
            SAMLAnyURIValue::fromString('urn:x-simplesamlphp:resource'),
            DecisionTypeEnum::Permit,
            [$action],
            $evidence,
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($authzDecisionStatement),
        );
    }
}
