<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Factory;

use Mockery as m;
use phpDocumentor\Reflection\DocBlock as DocBlockElement;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Enum_ as EnumElement;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\Method as MethodElement;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\String_;
use PhpParser\Comment\Doc;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Enum_ as EnumNode;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use stdClass;

use function current;

/**
 * @uses \phpDocumentor\Reflection\Php\Enum_
 * @uses \phpDocumentor\Reflection\Php\Constant
 * @uses \phpDocumentor\Reflection\Php\Method
 * @uses \phpDocumentor\Reflection\Php\Factory\Enum_::matches
 *
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\Enum_
 * @covers \phpDocumentor\Reflection\Php\Factory\AbstractFactory
 * @covers ::<protected>
 * @covers ::<private>
 */
final class Enum_Test extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $docblockFactory;

    protected function setUp(): void
    {
        $this->docblockFactory = $this->prophesize(DocBlockFactoryInterface::class);
        $this->fixture = new Enum_($this->docblockFactory->reveal());
    }

    /**
     * @covers ::matches
     */
    public function testMatches(): void
    {
        self::assertFalse($this->fixture->matches(self::createContext(null), new stdClass()));
        self::assertTrue(
            $this->fixture->matches(
                self::createContext(null),
                $this->prophesize(EnumNode::class)->reveal()
            )
        );
    }

    /**
     * @covers ::create
     */
    public function testSimpleCreate(): void
    {
        $containerMock = m::mock(StrategyContainer::class);
        $enumMock     = $this->buildEnumMock();
        $enumMock->shouldReceive('getDocComment')->andReturnNull();

        $result = $this->performCreate($enumMock, $containerMock);

        self::assertInstanceOf(EnumElement::class, $result);
        self::assertEquals('\Space\MyEnum', (string) $result->getFqsen());
    }

    /**
     * @covers ::create
     */
    public function testBackedEnumTypeIsSet(): void
    {
        $containerMock = m::mock(StrategyContainer::class);
        $enumMock     = $this->buildEnumMock();
        $enumMock->shouldReceive('getDocComment')->andReturnNull();
        $enumMock->scalarType = new Identifier('string');

        $result = $this->performCreate($enumMock, $containerMock);

        self::assertInstanceOf(EnumElement::class, $result);
        self::assertEquals('\Space\MyEnum', (string) $result->getFqsen());
        self::assertEquals(new String_(), $result->getBackedType());
    }

    /**
     * @covers ::create
     */
    public function testClassImplementingInterface(): void
    {
        $containerMock = m::mock(StrategyContainer::class);
        $enumMock     = $this->buildEnumMock();
        $enumMock->shouldReceive('getDocComment')->andReturnNull();
        $enumMock->extends    = 'Space\MyParent';
        $enumMock->implements = [
            new Name('MyInterface'),
        ];

        $result = $this->performCreate($enumMock, $containerMock);

        self::assertInstanceOf(EnumElement::class, $result);
        self::assertEquals('\Space\MyEnum', (string) $result->getFqsen());

        self::assertEquals(
            ['\MyInterface' => new Fqsen('\MyInterface')],
            $result->getInterfaces()
        );
    }

    /**
     * @covers ::create
     */
    public function testIteratesStatements(): void
    {
        $method1           = new ClassMethod('MyEnum::method1');
        $method1Descriptor = new MethodElement(new Fqsen('\MyEnum::method1'));
        $strategyMock      = $this->prophesize(ProjectFactoryStrategy::class);
        $containerMock     = $this->prophesize(StrategyContainer::class);
        $enumMock         = $this->buildEnumMock();
        $enumMock->shouldReceive('getDocComment')->andReturnNull();
        $enumMock->stmts = [$method1];

        $strategyMock->create(Argument::type(ContextStack::class), $method1, $containerMock)
            ->will(function ($args) use ($method1Descriptor): void {
                $args[0]->peek()->addMethod($method1Descriptor);
            })
            ->shouldBeCalled();

        $containerMock->findMatching(
            Argument::type(ContextStack::class),
            $method1
        )->willReturn($strategyMock->reveal());

        $result = $this->performCreate($enumMock, $containerMock->reveal());

        self::assertInstanceOf(EnumElement::class, $result);
        self::assertEquals('\Space\MyEnum', (string) $result->getFqsen());
        self::assertEquals(
            ['\MyEnum::method1' => $method1Descriptor],
            $result->getMethods()
        );
    }

    /**
     * @covers ::create
     */
    public function testCreateWithDocBlock(): void
    {
        $doc       = new Doc('Text');
        $enumMock = $this->buildEnumMock();
        $enumMock->shouldReceive('getDocComment')->andReturn($doc);
        $docBlock = new DocBlockElement('');
        $this->docblockFactory->create('Text', null)->willReturn($docBlock);
        $containerMock = m::mock(StrategyContainer::class);

        $result = $this->performCreate($enumMock, $containerMock);

        self::assertSame($docBlock, $result->getDocBlock());
    }

    /**
     * @return m\MockInterface|ClassNode
     */
    private function buildEnumMock()
    {
        $enumMock = m::mock(EnumNode::class);
        $enumMock->shouldReceive('getAttribute')->andReturn(new Fqsen('\Space\MyEnum'));
        $enumMock->implements = [];
        $enumMock->stmts = [];
        $enumMock->shouldReceive('getLine')->andReturn(1);
        $enumMock->shouldReceive('getEndLine')->andReturn(2);

        return $enumMock;
    }

    private function performCreate(EnumNode $enumMock, StrategyContainer $containerMock): EnumElement
    {
        $file = new File('hash', 'path');
        $this->fixture->create(self::createContext(null)->push($file), $enumMock, $containerMock);

        return current($file->getEnums());
    }
}
