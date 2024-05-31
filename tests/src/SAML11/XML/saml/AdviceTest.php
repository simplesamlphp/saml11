<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML11\XML\saml;

use DateTimeImmutable;
use DOMDocument;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML11\Compat\AbstractContainer;
use SimpleSAML\SAML11\Compat\ContainerSingleton;
use SimpleSAML\SAML11\XML\saml\AbstractAdviceType;
use SimpleSAML\SAML11\XML\saml\AbstractSamlElement;
use SimpleSAML\SAML11\XML\saml\AbstractStatement;
use SimpleSAML\SAML11\XML\saml\AbstractSubjectStatement;
use SimpleSAML\SAML11\XML\saml\Advice;
use SimpleSAML\SAML11\XML\saml\Assertion;
use SimpleSAML\SAML11\XML\saml\AssertionIDReference;
use SimpleSAML\SAML11\XML\saml\AttributeStatement;
use SimpleSAML\SAML11\XML\saml\AuthenticationStatement;
//use SimpleSAML\SAML11\XML\saml\AuthorizationDecisionStatement;
use SimpleSAML\SAML11\XML\saml\Conditions;
use SimpleSAML\Test\SAML11\CustomCondition;
use SimpleSAML\Test\SAML11\CustomStatement;
use SimpleSAML\Test\SAML11\CustomSubjectStatement;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\SAML11\XML\saml\AdviceTest
 *
 * @package simplesamlphp/saml11
 */
#[CoversClass(Advice::class)]
#[CoversClass(AbstractAdviceType::class)]
#[CoversClass(AbstractSamlElement::class)]
final class AdviceTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /** @var \SimpleSAML\SAML11\Compat\AbstractContainer */
    private static AbstractContainer $containerBackup;

    /** @var \SimpleSAML\XML\Chunk $chunk */
    private static Chunk $chunk;

    /** @var \DOMDocument $conditions */
    private static DOMDocument $conditions;

    /** @var \DOMDocument $statement */
    private static DOMDocument $statement;

    /** @var \DOMDocument $subjectStatement */
    private static DOMDocument $subjectStatement;

    /** @var \DOMDocument $authnStatement */
    private static DOMDocument $authnStatement;

    /** @var \DOMDocument $authzDecisionStatement */
//    private static DOMDocument $authzDecisionStatement;

    /** @var \DOMDocument $attributeStatement */
    private static DOMDocument $attributeStatement;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$containerBackup = ContainerSingleton::getInstance();

        self::$schemaFile = dirname(__FILE__, 5) . '/resources/schemas/simplesamlphp.xsd';

        self::$testedClass = Advice::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Advice.xml',
        );

        self::$conditions = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_Conditions.xml',
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

//        self::$authzDecisionStatement = DOMDocumentFactory::fromFile(
//            dirname(__FILE__, 5) . '/resources/xml/saml_AuthorizationDecisionStatement.xml',
//        );

        self::$attributeStatement = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 5) . '/resources/xml/saml_AttributeStatement.xml',
        );

        self::$chunk = new Chunk(DOMDocumentFactory::fromString(
            '<ssp:Chunk xmlns:ssp="urn:x-simplesamlphp:namespace">some</ssp:Chunk>',
        )->documentElement);


        $container = clone self::$containerBackup;
        $container->registerExtensionHandler(CustomCondition::class);
        $container->registerExtensionHandler(CustomStatement::class);
        $container->registerExtensionHandler(CustomSubjectStatement::class);
        ContainerSingleton::setContainer($container);
    }


    /**
     */
    public static function tearDownAfterClass(): void
    {
        ContainerSingleton::setContainer(self::$containerBackup);
    }


    /**
     */
    public function testMarshalling(): void
    {
        $assertionIDReference = new AssertionIDReference('_Test');

        $assertion = new Assertion(
            '_abc123',
            'urn:x-simplesamlphp:phpunit',
            new DateTimeImmutable('2023-01-24T09:42:26Z'),
            Conditions::fromXML(self::$conditions->documentElement),
            null, // null
            [
                AbstractStatement::fromXML(self::$statement->documentElement),
                AbstractSubjectStatement::fromXML(self::$subjectStatement->documentElement),
                AuthenticationStatement::fromXML(self::$authnStatement->documentElement),
                //null, // authzDecisionStatement
                AttributeStatement::fromXML(self::$attributeStatement->documentElement),
            ],
        );

        $advice = new Advice(
            [$assertionIDReference],
            [$assertion],
            [self::$chunk],
        );

        $assertion = new Assertion(
            '_abc123',
            'urn:x-simplesamlphp:phpunit',
            new DateTimeImmutable('2023-01-24T09:42:26Z'),
            Conditions::fromXML(self::$conditions->documentElement),
            $advice,
            [
                AbstractStatement::fromXML(self::$statement->documentElement),
                AbstractSubjectStatement::fromXML(self::$subjectStatement->documentElement),
                AuthenticationStatement::fromXML(self::$authnStatement->documentElement),
                //null, // authzDecisionStatement
                AttributeStatement::fromXML(self::$attributeStatement->documentElement),
            ],
        );

        $advice = new Advice(
            [$assertionIDReference],
            [$assertion],
            [self::$chunk],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($advice),
        );
    }

    /**
     */
    public function testMarshallingEmpty(): void
    {
        $advice = new Advice();
        $this->assertEquals(
            '<saml:Advice xmlns:saml="urn:oasis:names:tc:SAML:1.0:assertion"/>',
            strval($advice),
        );
        $this->assertTrue($advice->isEmptyElement());
    }
}
