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
use phpDocumentor\Reflection\Php\Class_ as ClassElement;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\Method as MethodElement;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Comment\Doc;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\ClassMethod;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use stdClass;

use function current;

/**
 * @uses \phpDocumentor\Reflection\Php\Class_
 * @uses \phpDocumentor\Reflection\Php\Constant
 * @uses \phpDocumentor\Reflection\Php\Property
 * @uses \phpDocumentor\Reflection\Php\Visibility
 * @uses \phpDocumentor\Reflection\Php\Method
 * @uses \phpDocumentor\Reflection\Php\Factory\Class_::matches
 * @uses \phpDocumentor\Reflection\Php\Factory\ClassConstantIterator
 * @uses \phpDocumentor\Reflection\Php\Factory\PropertyIterator
 *
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\Class_
 * @covers \phpDocumentor\Reflection\Php\Factory\AbstractFactory
 * @covers ::<protected>
 * @covers ::<private>
 */
final class Class_Test extends TestCase
{
    /** @var ObjectProphecy */
    private $docblockFactory;

    protected function setUp(): void
    {
        $this->docblockFactory = $this->prophesize(DocBlockFactoryInterface::class);
        $this->fixture = new Class_($this->docblockFactory->reveal());
    }

    /**
     * @covers ::matches
     */
    public function testMatches(): void
    {
        $this->assertFalse($this->fixture->matches(self::createContext(null), new stdClass()));
        $this->assertTrue(
            $this->fixture->matches(
                self::createContext(null),
                $this->prophesize(ClassNode::class)->reveal()
            )
        );
    }

    /**
     * @covers ::create
     */
    public function testSimpleCreate(): void
    {
        $containerMock = m::mock(StrategyContainer::class);
        $classMock     = $this->buildClassMock();
        $classMock->shouldReceive('getDocComment')->andReturnNull();

        $class = $this->performCreate($classMock, $containerMock);

        $this->assertInstanceOf(ClassElement::class, $class);
        $this->assertEquals('\Space\MyClass', (string) $class->getFqsen());
        $this->assertNull($class->getParent());
        $this->assertTrue($class->isFinal());
        $this->assertTrue($class->isAbstract());
    }

    /**
     * @covers ::create
     */
    public function testClassWithParent(): void
    {
        $containerMock = m::mock(StrategyContainer::class);
        $classMock     = $this->buildClassMock();
        $classMock->shouldReceive('getDocComment')->andReturnNull();
        $classMock->extends = 'Space\MyParent';

        $class = $this->performCreate($classMock, $containerMock);

        $this->assertInstanceOf(ClassElement::class, $class);
        $this->assertEquals('\Space\MyClass', (string) $class->getFqsen());
        $this->assertEquals('\Space\MyParent', (string) $class->getParent());
    }

    /**
     * @covers ::create
     */
    public function testClassImplementingInterface(): void
    {
        $containerMock = m::mock(StrategyContainer::class);
        $classMock     = $this->buildClassMock();
        $classMock->shouldReceive('getDocComment')->andReturnNull();
        $classMock->extends    = 'Space\MyParent';
        $classMock->implements = [
            new Name('MyInterface'),
        ];

        $class = $this->performCreate($classMock, $containerMock);

        $this->assertInstanceOf(ClassElement::class, $class);
        $this->assertEquals('\Space\MyClass', (string) $class->getFqsen());

        $this->assertEquals(
            ['\MyInterface' => new Fqsen('\MyInterface')],
            $class->getInterfaces()
        );
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

        $this->assertInstanceOf(ClassElement::class, $class);
        $this->assertEquals('\Space\MyClass', (string) $class->getFqsen());
        $this->assertEquals(
            ['\MyClass::method1' => $method1Descriptor],
            $class->getMethods()
        );
    }

    /**
     * @covers ::create
     */
    public function testCreateWithDocBlock(): void
    {
        $doc       = new Doc('Text');
        $classMock = $this->buildClassMock();
        $classMock->shouldReceive('getDocComment')->andReturn($doc);
        $docBlock = new DocBlockElement('');
        $this->docblockFactory->create('Text', null)->willReturn($docBlock);
        $containerMock = m::mock(StrategyContainer::class);

        $class = $this->performCreate($classMock, $containerMock);

        $this->assertSame($docBlock, $class->getDocBlock());
    }

    /**
     * @return m\MockInterface|ClassNode
     */
    private function buildClassMock()
    {
        $classMock        = m::mock(ClassNode::class);
        $classMock->fqsen = new Fqsen('\Space\MyClass');
        $classMock->shouldReceive('isFinal')->andReturn(true);
        $classMock->shouldReceive('isAbstract')->andReturn(true);
        $classMock->shouldReceive('getLine')->andReturn(1);
        $classMock->shouldReceive('getEndLine')->andReturn(2);

        return $classMock;
    }

    private function performCreate(ClassNode $classMock, StrategyContainer $containerMock): ClassElement
    {
        $file = new File('hash', 'path');
        $this->fixture->create(self::createContext(null)->push($file), $classMock, $containerMock);

        return current($file->getClasses());
    }
}
