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
use phpDocumentor\Reflection\Php\File as FileElement;
use phpDocumentor\Reflection\Php\Interface_ as InterfaceElement;
use phpDocumentor\Reflection\Php\Method as MethodElement;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Interface_ as InterfaceNode;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use stdClass;

use function current;

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
    use ProphecyTrait;

    private ObjectProphecy $docBlockFactory;

    protected function setUp(): void
    {
        $this->docBlockFactory = $this->prophesize(DocBlockFactoryInterface::class);
        $this->fixture = new Interface_($this->docBlockFactory->reveal());
    }

    /**
     * @covers ::matches
     */
    public function testMatches(): void
    {
        $this->assertFalse($this->fixture->matches(self::createContext(null), new stdClass()));
        $this->assertTrue($this->fixture->matches(self::createContext(null), m::mock(InterfaceNode::class)));
    }

    /**
     * @covers ::create
     */
    public function testSimpleCreate(): void
    {
        $interfaceMock = $this->buildClassMock();
        $interfaceMock->shouldReceive('getDocComment')->andReturnNull();
        $containerMock = $this->prophesize(StrategyContainer::class);

        $interface = $this->performCreate($interfaceMock, $containerMock->reveal());

        $this->assertInstanceOf(InterfaceElement::class, $interface);
        $this->assertEquals('\Space\MyInterface', (string) $interface->getFqsen());
    }

    /**
     * @covers ::create
     */
    public function testCreateWithDocBlock(): void
    {
        $doc           = new Doc('Text');
        $docBlock      = new DocBlockElement('');
        $this->docBlockFactory->create('Text', null)->willReturn($docBlock);
        $containerMock = $this->prophesize(StrategyContainer::class);

        $interfaceMock = $this->buildClassMock();
        $interfaceMock->shouldReceive('getDocComment')->andReturn($doc);

        $interface = $this->performCreate($interfaceMock, $containerMock->reveal());

        $this->assertSame($docBlock, $interface->getDocBlock());
    }

    /**
     * @covers ::create
     */
    public function testIteratesStatements(): void
    {
        $method1           = new ClassMethod('MyClass::method1');
        $method1Descriptor = new MethodElement(new Fqsen('\MyClass::method1'));
        $strategyMock      = $this->prophesize(ProjectFactoryStrategy::class);
        $containerMock     = $this->prophesize(StrategyContainer::class);
        $classMock         = $this->buildClassMock();
        $classMock->shouldReceive('getDocComment')->andReturnNull();
        $classMock->stmts = [$method1];

        $strategyMock->create(Argument::type(ContextStack::class), $method1, $containerMock)
            ->will(function ($args) use ($method1Descriptor): void {
                $args[0]->peek()->addMethod($method1Descriptor);
            })
            ->shouldBeCalled();

        $containerMock->findMatching(
            Argument::type(ContextStack::class),
            $method1
        )->willReturn($strategyMock->reveal());

        $class = $this->performCreate($classMock, $containerMock->reveal());

        $this->assertInstanceOf(InterfaceElement::class, $class);
        $this->assertEquals('\Space\MyInterface', (string) $class->getFqsen());
        $this->assertEquals(
            ['\MyClass::method1' => $method1Descriptor],
            $class->getMethods()
        );
    }

    /**
     * @return m\MockInterface|InterfaceNode
     */
    private function buildClassMock()
    {
        $interfaceMock          = m::mock(InterfaceNode::class);
        $interfaceMock->extends = [];
        $interfaceMock->stmts = [];
        $interfaceMock->shouldReceive('getAttribute')->andReturn(new Fqsen('\Space\MyInterface'));
        $interfaceMock->shouldReceive('getLine')->andReturn(1);
        $interfaceMock->shouldReceive('getEndLine')->andReturn(2);

        return $interfaceMock;
    }

    private function performCreate(m\MockInterface $interfaceMock, StrategyContainer $containerMock): InterfaceElement
    {
        $file = new FileElement('hash', 'path');
        $this->fixture->create(
            self::createContext(null)->push($file),
            $interfaceMock,
            $containerMock
        );

        return current($file->getInterfaces());
    }
}
