<?php

declare(strict_types=1);

/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Factory;

use Mockery as m;
use Mockery\MockInterface;
use phpDocumentor\Reflection\DocBlock as DocBlockDescriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Argument as ArgumentDescriptor;
use phpDocumentor\Reflection\Php\Method as MethodDescriptor;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Nullable;
use PhpParser\Comment\Doc;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\ClassMethod;
use stdClass;

/**
 * Test case for \phpDocumentor\Reflection\Php\Factory\Method
 *
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\Method
 * @covers ::<!public>
 */
class MethodTest extends TestCase
{
    protected function setUp() : void
    {
        $this->fixture = new Method();
    }

    /**
     * @covers ::matches
     */
    public function testMatches() : void
    {
        $this->assertFalse($this->fixture->matches(new stdClass()));
        $this->assertTrue($this->fixture->matches(m::mock(ClassMethod::class)));
    }

    /**
     * @covers ::create
     */
    public function testCreateWithoutParameters() : void
    {
        $classMethodMock         = $this->buildClassMethodMock();
        $classMethodMock->params = [];
        $classMethodMock->shouldReceive('isPrivate')->once()->andReturn(false);
        $classMethodMock->shouldReceive('isProtected')->once()->andReturn(false);
        $classMethodMock->shouldReceive('getDocComment')->once()->andReturnNull();
        $classMethodMock->shouldReceive('getReturnType')->once()->andReturn(null);

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching')->never();

        /** @var MethodDescriptor $method */
        $method = $this->fixture->create($classMethodMock, $containerMock);

        $this->assertEquals('\SomeSpace\Class::function()', (string) $method->getFqsen());
        $this->assertEquals('public', (string) $method->getVisibility());
    }

    public function testCreateProtectedMethod() : void
    {
        $classMethodMock         = $this->buildClassMethodMock();
        $classMethodMock->params = [];
        $classMethodMock->shouldReceive('isPrivate')->once()->andReturn(false);
        $classMethodMock->shouldReceive('isProtected')->once()->andReturn(true);
        $classMethodMock->shouldReceive('getDocComment')->once()->andReturnNull();
        $classMethodMock->shouldReceive('getReturnType')->once()->andReturn(null);

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching')->never();

        /** @var MethodDescriptor $method */
        $method = $this->fixture->create($classMethodMock, $containerMock);

        $this->assertEquals('\SomeSpace\Class::function()', (string) $method->getFqsen());
        $this->assertEquals('protected', (string) $method->getVisibility());
    }

    /**
     * @covers ::create
     */
    public function testCreateWithParameters() : void
    {
        $classMethodMock         = $this->buildClassMethodMock();
        $classMethodMock->params = ['param1'];
        $classMethodMock->shouldReceive('isPrivate')->once()->andReturn(true);
        $classMethodMock->shouldReceive('getDocComment')->once()->andReturnNull();
        $classMethodMock->shouldReceive('getReturnType')->once()->andReturn(null);

        $strategyMock  = m::mock(ProjectFactoryStrategy::class);
        $containerMock = m::mock(StrategyContainer::class);

        $strategyMock->shouldReceive('create')
            ->with('param1', $containerMock, null)
            ->andReturn(new ArgumentDescriptor('param1'));

        $containerMock->shouldReceive('findMatching')
            ->with('param1')
            ->andReturn($strategyMock);

        /** @var MethodDescriptor $method */
        $method = $this->fixture->create($classMethodMock, $containerMock);

        $this->assertEquals('\SomeSpace\Class::function()', (string) $method->getFqsen());
        $this->assertTrue($method->isAbstract());
        $this->assertTrue($method->isFinal());
        $this->assertTrue($method->isStatic());
        $this->assertEquals('private', (string) $method->getVisibility());
    }

    /**
     * @covers ::create
     */
    public function testReturnTypeResolving() : void
    {
        $classMethodMock         = $this->buildClassMethodMock();
        $classMethodMock->params = [];
        $classMethodMock->shouldReceive('isPrivate')->once()->andReturn(true);
        $classMethodMock->shouldReceive('getDocComment')->once()->andReturnNull();
        $classMethodMock->shouldReceive('getReturnType')->times(4)->andReturn(new Name('int'));

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching')->never();

        /** @var MethodDescriptor $method */
        $method = $this->fixture->create($classMethodMock, $containerMock);

        $this->assertEquals(new Integer(), $method->getReturnType());
    }

    /**
     * @covers ::create
     */
    public function testReturnTypeNullableResolving() : void
    {
        $classMethodMock         = $this->buildClassMethodMock();
        $classMethodMock->params = [];
        $classMethodMock->shouldReceive('isPrivate')->once()->andReturn(true);
        $classMethodMock->shouldReceive('getDocComment')->once()->andReturnNull();
        $classMethodMock->shouldReceive('getReturnType')->times(3)->andReturn(new NullableType('int'));

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching')->never();

        /** @var MethodDescriptor $method */
        $method = $this->fixture->create($classMethodMock, $containerMock);

        $this->assertEquals(new Nullable(new Integer()), $method->getReturnType());
    }

    /**
     * @covers ::create
     */
    public function testCreateWithDocBlock() : void
    {
        $doc                     = m::mock(Doc::class);
        $classMethodMock         = $this->buildClassMethodMock();
        $classMethodMock->params = [];
        $classMethodMock->shouldReceive('isPrivate')->once()->andReturn(true);
        $classMethodMock->shouldReceive('getDocComment')->andReturn($doc);
        $classMethodMock->shouldReceive('getReturnType')->once()->andReturn(null);

        $docBlock      = new DocBlockDescriptor('');
        $strategyMock  = m::mock(ProjectFactoryStrategy::class);
        $containerMock = m::mock(StrategyContainer::class);

        $strategyMock->shouldReceive('create')
            ->with($doc, $containerMock, null)
            ->andReturn($docBlock);

        $containerMock->shouldReceive('findMatching')
            ->with($doc)
            ->andReturn($strategyMock);

        /** @var MethodDescriptor $method */
        $method = $this->fixture->create($classMethodMock, $containerMock);

        $this->assertEquals('\SomeSpace\Class::function()', (string) $method->getFqsen());
        $this->assertSame($docBlock, $method->getDocBlock());
    }

    /**
     * @return MockInterface|ClassMethod
     */
    private function buildClassMethodMock() : MockInterface
    {
        $methodMock        = m::mock(ClassMethod::class);
        $methodMock->name  = 'function';
        $methodMock->fqsen = new Fqsen('\SomeSpace\Class::function()');

        $methodMock->shouldReceive('isStatic')->once()->andReturn(true);
        $methodMock->shouldReceive('isFinal')->once()->andReturn(true);
        $methodMock->shouldReceive('isAbstract')->once()->andReturn(true);
        $methodMock->shouldReceive('getLine')->once()->andReturn(1);

        return $methodMock;
    }
}
