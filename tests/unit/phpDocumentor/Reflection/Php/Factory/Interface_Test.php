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
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Constant as ConstantElement;
use phpDocumentor\Reflection\Php\Interface_ as InterfaceElement;
use phpDocumentor\Reflection\Php\Method as MethodElement;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Comment\Doc;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Interface_ as InterfaceNode;
use stdClass;

/**
 * @uses \phpDocumentor\Reflection\Php\Interface_
 * @uses \phpDocumentor\Reflection\Php\Constant
 * @uses \phpDocumentor\Reflection\Php\Method
 * @uses \phpDocumentor\Reflection\Php\Visibility
 * @uses \phpDocumentor\Reflection\Php\Factory\Interface_::matches
 * @uses \phpDocumentor\Reflection\Php\Factory\ClassConstantIterator
 *
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\Interface_
 * @covers \phpDocumentor\Reflection\Php\Factory\AbstractFactory
 * @covers ::<private>
 * @covers ::<protected>
 */
class Interface_Test extends TestCase
{
    protected function setUp() : void
    {
        $this->fixture = new Interface_();
    }

    /**
     * @covers ::matches
     */
    public function testMatches() : void
    {
        $this->assertFalse($this->fixture->matches(new stdClass()));
        $this->assertTrue($this->fixture->matches(m::mock(InterfaceNode::class)));
    }

    /**
     * @covers ::create
     */
    public function testSimpleCreate() : void
    {
        $containerMock = m::mock(StrategyContainer::class);
        $interfaceMock = $this->buildClassMock();
        $interfaceMock->shouldReceive('getDocComment')->andReturnNull();

        /** @var InterfaceElement $class */
        $class = $this->fixture->create($interfaceMock, $containerMock);

        $this->assertInstanceOf(InterfaceElement::class, $class);
        $this->assertEquals('\Space\MyInterface', (string) $class->getFqsen());
    }

    /**
     * @covers ::create
     */
    public function testCreateWithDocBlock() : void
    {
        $doc           = m::mock(Doc::class);
        $interfaceMock = $this->buildClassMock();
        $interfaceMock->shouldReceive('getDocComment')->andReturn($doc);

        $docBlock      = new DocBlockElement('');
        $strategyMock  = m::mock(ProjectFactoryStrategy::class);
        $containerMock = m::mock(StrategyContainer::class);

        $strategyMock->shouldReceive('create')
            ->with($doc, $containerMock, null)
            ->andReturn($docBlock);

        $containerMock->shouldReceive('findMatching')
            ->with($doc)
            ->andReturn($strategyMock);

        /** @var InterfaceElement $interface */
        $interface = $this->fixture->create($interfaceMock, $containerMock);

        $this->assertSame($docBlock, $interface->getDocBlock());
    }

    /**
     * @covers ::create
     */
    public function testWithMethodMembers() : void
    {
        $method1           = new ClassMethod('\Space\MyInterface::method1');
        $method1Descriptor = new MethodElement(new Fqsen('\Space\MyInterface::method1'));
        $strategyMock      = m::mock(ProjectFactoryStrategy::class);
        $containerMock     = m::mock(StrategyContainer::class);
        $interfaceMock     = $this->buildClassMock();
        $interfaceMock->shouldReceive('getDocComment')->andReturnNull();
        $interfaceMock->stmts = [$method1];

        $strategyMock->shouldReceive('create')
            ->with($method1, $containerMock, null)
            ->andReturn($method1Descriptor);

        $containerMock->shouldReceive('findMatching')
            ->with($method1)
            ->andReturn($strategyMock);

        $this->fixture->create($interfaceMock, $containerMock);

        /** @var InterfaceElement $interface */
        $interface = $this->fixture->create($interfaceMock, $containerMock);

        $this->assertInstanceOf(InterfaceElement::class, $interface);
        $this->assertEquals('\Space\MyInterface', (string) $interface->getFqsen());
        $this->assertEquals(
            ['\Space\MyInterface::method1' => $method1Descriptor],
            $interface->getMethods()
        );
    }

    /**
     * @covers ::create
     */
    public function testWithConstants() : void
    {
        $const    = new Const_('\Space\MyClass::MY_CONST', new Variable('a'));
        $constant = new ClassConst([$const]);

        $result        = new ConstantElement(new Fqsen('\Space\MyClass::MY_CONST'));
        $strategyMock  = m::mock(ProjectFactoryStrategy::class);
        $containerMock = m::mock(StrategyContainer::class);

        $strategyMock->shouldReceive('create')
            ->with(m::type(ClassConstantIterator::class), $containerMock, null)
            ->andReturn($result);

        $containerMock->shouldReceive('findMatching')
            ->with(m::type(ClassConstantIterator::class))
            ->andReturn($strategyMock);

        $classMock = $this->buildClassMock();
        $classMock->shouldReceive('getDocComment')->andReturnNull();
        $classMock->stmts = [$constant];

        /** @var ClassElement $class */
        $class = $this->fixture->create($classMock, $containerMock);

        $this->assertEquals(
            ['\Space\MyClass::MY_CONST' => $result],
            $class->getConstants()
        );
    }

    /**
     * @return m\MockInterface|InterfaceNode
     */
    private function buildClassMock()
    {
        $interfaceMock          = m::mock(InterfaceNode::class);
        $interfaceMock->fqsen   = new Fqsen('\Space\MyInterface');
        $interfaceMock->extends = [];
        $interfaceMock->shouldReceive('getLine')->andReturn(1);
        return $interfaceMock;
    }
}
