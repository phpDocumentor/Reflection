<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Class_ as ClassElement;
use phpDocumentor\Reflection\Php\Interface_ as InterfaceElement;
use phpDocumentor\Reflection\Php\Project;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\Property as PropertyElement;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Php\Visibility;
use PhpParser\Comment\Doc;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\PrettyPrinter\Standard;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use stdClass;

use function current;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\ConstructorPromotion
 */
final class ConstructorPromotionTest extends TestCase
{
    /** @var ObjectProphecy */
    private $strategy;
    /** @var ObjectProphecy */
    private $docblockFactory;

    protected function setUp(): void
    {
        $this->strategy        = $this->prophesize(ProjectFactoryStrategy::class);
        $this->docblockFactory = $this->prophesize(DocBlockFactoryInterface::class);
        $printer               = $this->prophesize(Standard::class);
        $printer->prettyPrintExpr(Argument::any())->willReturn('myType');

        $this->fixture = new ConstructorPromotion(
            $this->strategy->reveal(),
            $this->docblockFactory->reveal(),
            $printer->reveal()
        );
    }

    /**
     * @dataProvider objectProvider
     * @covers ::__construct
     * @covers ::matches
     */
    public function testMatches(ContextStack $context, object $object, bool $expected): void
    {
        self::assertEquals($expected, $this->fixture->matches($context, $object));
    }

    /**
     * @return mixed[][]
     */
    public function objectProvider(): array
    {
        $context = new ContextStack(new Project('test'));

        return [
            'emptyContext' => [
                $context,
                new stdClass(),
                false,
            ],
            'invalid stack type' => [
                $context->push(new InterfaceElement(new Fqsen('\MyInterface'))),
                new ClassMethod('foo'),
                false,
            ],
            'with class but not constructor' => [
                $context->push(new ClassElement(new Fqsen('\MyInterface'))),
                new ClassMethod('foo'),
                false,
            ],
            'with class but and is constructor' => [
                $context->push(new ClassElement(new Fqsen('\MyInterface'))),
                new ClassMethod('__construct'),
                true,
            ],
        ];
    }

    /**
     * @covers ::buildPropertyVisibilty
     * @covers ::doCreate
     * @covers ::promoteParameterToProperty
     * @covers ::readOnly
     * @dataProvider visibilityProvider
     */
    public function testCreateWithProperty(int $flags, string $visibility, bool $readOnly = false): void
    {
        $methodNode         = new ClassMethod('__construct');
        $methodNode->params = [
            new Param(
                new Variable('myArgument'),
                new String_('MyDefault'),
                new Identifier('string'),
                false,
                false,
                [
                    'comments' => [
                        new Doc('text'),
                    ],
                ],
                $flags
            ),
        ];

        $docBlock = new DocBlock('Test');
        $class    = new ClassElement(new Fqsen('\MyClass'));
        $context  = self::createContext()->push($class);

        $this->docblockFactory->create('text', null)->willReturn($docBlock);
        $this->strategy->create($context, $methodNode, Argument::type(StrategyContainer::class))
            ->shouldBeCalled();

        $this->fixture->create(
            $context,
            $methodNode,
            $this->prophesize(StrategyContainer::class)->reveal()
        );

        $property = current($class->getProperties());

        self::assertInstanceOf(PropertyElement::class, $property);
        self::assertEquals($visibility, $property->getVisibility());
        self::assertSame($docBlock, $property->getDocBlock());
        self::assertSame('myType', $property->getDefault());
        self::assertEquals('\MyClass::$myArgument', $property->getFqsen());
        self::assertSame($readOnly, $property->isReadOnly());
    }

    /** @return mixed[][] */
    public function visibilityProvider(): array
    {
        return [
            [
                ClassNode::MODIFIER_PUBLIC,
                Visibility::PUBLIC_,
            ],
            [
                ClassNode::MODIFIER_PROTECTED,
                Visibility::PROTECTED_,
            ],
            [
                ClassNode::MODIFIER_PRIVATE,
                Visibility::PRIVATE_,
            ],
            [
                ClassNode::MODIFIER_PRIVATE | ClassNode::MODIFIER_READONLY,
                Visibility::PRIVATE_,
                true,
            ],
        ];
    }
}
