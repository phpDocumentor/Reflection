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
use Mockery\MockInterface;
use phpDocumentor\Reflection\DocBlock as DocBlockDescriptor;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Class_ as ClassElement;
use phpDocumentor\Reflection\Php\Method as MethodDescriptor;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Comment\Doc;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use stdClass;

use function current;

/**
 * @uses   \phpDocumentor\Reflection\Php\Method
 * @uses   \phpDocumentor\Reflection\Php\Argument
 * @uses   \phpDocumentor\Reflection\Php\Visibility
 * @uses   \phpDocumentor\Reflection\Php\Factory\Method::matches
 * @uses   \phpDocumentor\Reflection\Php\Factory\Type
 *
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\Method
 * @covers \phpDocumentor\Reflection\Php\Factory\AbstractFactory
 * @covers ::<protected>
 * @covers ::<private>
 */
class MethodTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $docBlockFactory;

    protected function setUp(): void
    {
        $this->docBlockFactory = $this->prophesize(DocBlockFactoryInterface::class);
        $this->fixture = new Method($this->docBlockFactory->reveal());
    }

    /**
     * @covers ::matches
     */
    public function testMatches(): void
    {
        $this->assertFalse($this->fixture->matches(self::createContext(null), new stdClass()));
        $this->assertTrue($this->fixture->matches(self::createContext(null), m::mock(ClassMethod::class)));
    }

    /**
     * @covers ::create
     */
    public function testCreateWithoutParameters(): void
    {
        $classMethodMock = $this->buildClassMethodMock();
        $classMethodMock->params = [];
        $classMethodMock->shouldReceive('isPrivate')->once()->andReturn(false);
        $classMethodMock->shouldReceive('isProtected')->once()->andReturn(false);
        $classMethodMock->shouldReceive('getDocComment')->once()->andReturnNull();
        $classMethodMock->shouldReceive('getReturnType')->once()->andReturn(null);

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching')->never();

        $class = new ClassElement(new Fqsen('\\MyClass'));
        $this->fixture->create(self::createContext(null)->push($class), $classMethodMock, $containerMock);

        $method = current($class->getMethods());
        $this->assertInstanceOf(MethodDescriptor::class, $method);
        $this->assertEquals('\SomeSpace\Class::function()', (string) $method->getFqsen());
        $this->assertEquals('public', (string) $method->getVisibility());
    }

    /**
     * @covers ::create
     */
    public function testCreateProtectedMethod(): void
    {
        $classMethodMock = $this->buildClassMethodMock();
        $classMethodMock->params = [];
        $classMethodMock->shouldReceive('isPrivate')->once()->andReturn(false);
        $classMethodMock->shouldReceive('isProtected')->once()->andReturn(true);
        $classMethodMock->shouldReceive('getDocComment')->once()->andReturnNull();
        $classMethodMock->shouldReceive('getReturnType')->once()->andReturn(null);

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching')->never();

        $class = new ClassElement(new Fqsen('\\MyClass'));
        $this->fixture->create(self::createContext(null)->push($class), $classMethodMock, $containerMock);

        $method = current($class->getMethods());
        $this->assertInstanceOf(MethodDescriptor::class, $method);
        $this->assertEquals('\SomeSpace\Class::function()', (string) $method->getFqsen());
        $this->assertEquals('protected', (string) $method->getVisibility());
    }

    /**
     * @covers ::create
     */
    public function testCreateWithParameters(): void
    {
        $param1 = new Param(new Variable('param1'));
        $classMethodMock = $this->buildClassMethodMock();
        $classMethodMock->params = [$param1];
        $classMethodMock->shouldReceive('isPrivate')->once()->andReturn(true);
        $classMethodMock->shouldReceive('getDocComment')->once()->andReturnNull();
        $classMethodMock->shouldReceive('getReturnType')->once()->andReturn(null);

        $argumentStrategy = $this->prophesize(ProjectFactoryStrategy::class);
        $containerMock = $this->prophesize(StrategyContainer::class);
        $containerMock->findMatching(
            Argument::type(ContextStack::class),
            $param1
        )->willReturn($argumentStrategy);

        $argumentStrategy->create(
            Argument::that(static fn ($agument): bool => $agument->peek() instanceof MethodDescriptor),
            $param1,
            $containerMock->reveal()
        )->shouldBeCalled();

        $class = new ClassElement(new Fqsen('\\MyClass'));
        $this->fixture->create(self::createContext(null)->push($class), $classMethodMock, $containerMock->reveal());

        $method = current($class->getMethods());
        $this->assertInstanceOf(MethodDescriptor::class, $method);
        $this->assertEquals('\SomeSpace\Class::function()', (string) $method->getFqsen());
        $this->assertTrue($method->isAbstract());
        $this->assertTrue($method->isFinal());
        $this->assertTrue($method->isStatic());
        $this->assertEquals('private', (string) $method->getVisibility());
    }

    /**
     * @covers ::create
     */
    public function testCreateWithDocBlock(): void
    {
        $doc = new Doc('Text');
        $classMethodMock = $this->buildClassMethodMock();
        $classMethodMock->params = [];
        $classMethodMock->shouldReceive('isPrivate')->once()->andReturn(true);
        $classMethodMock->shouldReceive('getDocComment')->andReturn($doc);
        $classMethodMock->shouldReceive('getReturnType')->once()->andReturn(null);

        $docBlock = new DocBlockDescriptor('');
        $this->docBlockFactory->create('Text', null)->willReturn($docBlock);
        $containerMock = $this->prophesize(StrategyContainer::class);

        $class = new ClassElement(new Fqsen('\\MyClass'));
        $this->fixture->create(self::createContext(null)->push($class), $classMethodMock, $containerMock->reveal());

        $method = current($class->getMethods());
        $this->assertInstanceOf(MethodDescriptor::class, $method);
        $this->assertEquals('\SomeSpace\Class::function()', (string) $method->getFqsen());
        $this->assertSame($docBlock, $method->getDocBlock());
    }

    /**
     * @return MockInterface|ClassMethod
     */
    private function buildClassMethodMock(): MockInterface
    {
        $methodMock = m::mock(ClassMethod::class);
        $methodMock->name = 'function';
        $methodMock->shouldReceive('getAttribute')->andReturn(new Fqsen('\SomeSpace\Class::function()'));
        $methodMock->params = [];

        $methodMock->shouldReceive('isStatic')->once()->andReturn(true);
        $methodMock->shouldReceive('isFinal')->once()->andReturn(true);
        $methodMock->shouldReceive('isAbstract')->once()->andReturn(true);
        $methodMock->shouldReceive('getLine')->once()->andReturn(1);
        $methodMock->shouldReceive('getStartFilePos')->once()->andReturn(10);
        $methodMock->shouldReceive('getEndLine')->once()->andReturn(2);
        $methodMock->shouldReceive('getEndFilePos')->once()->andReturn(20);

        return $methodMock;
    }

    /**
     * @covers ::create
     */
    public function testIteratesStatements(): void
    {
        $method1 = $this->buildClassMethodMock();
        $method1->shouldReceive('isPrivate')->once()->andReturn(true);
        $method1->shouldReceive('getDocComment')->andReturn(null);
        $method1->shouldReceive('getReturnType')->once()->andReturn(null);
        $method1->stmts = [new Expression(new FuncCall(new Name('hook')))];

        $strategyMock = $this->prophesize(ProjectFactoryStrategy::class);

        $containerMock = $this->prophesize(StrategyContainer::class);
        $containerMock->findMatching(
            Argument::type(ContextStack::class),
            Argument::type(Expression::class)
        )->willReturn($strategyMock->reveal())->shouldBeCalledOnce();

        $class = new ClassElement(new Fqsen('\\MyClass'));
        $this->fixture->create(self::createContext(null)->push($class), $method1, $containerMock->reveal());
    }
}
