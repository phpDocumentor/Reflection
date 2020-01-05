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
use phpDocumentor\Reflection\Php\Class_ as ClassElement;
use phpDocumentor\Reflection\Php\Constant as ConstantElement;
use phpDocumentor\Reflection\Php\Method as MethodElement;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\Property as PropertyElement;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Comment\Doc;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property as PropertyNode;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Stmt\TraitUse;
use stdClass;

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
    protected function setUp() : void
    {
        $this->fixture = new Class_();
    }

    /**
     * @covers ::matches
     */
    public function testMatches() : void
    {
        $this->assertFalse($this->fixture->matches(new stdClass()));
        $this->assertTrue($this->fixture->matches(m::mock(ClassNode::class)));
    }

    /**
     * @covers ::create
     */
    public function testSimpleCreate() : void
    {
        $containerMock = m::mock(StrategyContainer::class);
        $classMock     = $this->buildClassMock();
        $classMock->shouldReceive('getDocComment')->andReturnNull();

        /** @var ClassElement $class */
        $class = $this->fixture->create($classMock, $containerMock);

        $this->assertInstanceOf(ClassElement::class, $class);
        $this->assertEquals('\Space\MyClass', (string) $class->getFqsen());
        $this->assertNull($class->getParent());
        $this->assertTrue($class->isFinal());
        $this->assertTrue($class->isAbstract());
    }

    /**
     * @covers ::create
     */
    public function testClassWithParent() : void
    {
        $containerMock = m::mock(StrategyContainer::class);
        $classMock     = $this->buildClassMock();
        $classMock->shouldReceive('getDocComment')->andReturnNull();
        $classMock->extends = 'Space\MyParent';

        /** @var ClassElement $class */
        $class = $this->fixture->create($classMock, $containerMock);

        $this->assertInstanceOf(ClassElement::class, $class);
        $this->assertEquals('\Space\MyClass', (string) $class->getFqsen());
        $this->assertEquals('\Space\MyParent', (string) $class->getParent());
    }

    /**
     * @covers ::create
     */
    public function testClassImplementingInterface() : void
    {
        $containerMock = m::mock(StrategyContainer::class);
        $classMock     = $this->buildClassMock();
        $classMock->shouldReceive('getDocComment')->andReturnNull();
        $classMock->extends    = 'Space\MyParent';
        $classMock->implements = [
            new Name('MyInterface'),
        ];

        /** @var ClassElement $class */
        $class = $this->fixture->create($classMock, $containerMock);

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
    public function testWithMethodMembers() : void
    {
        $method1           = new ClassMethod('MyClass::method1');
        $method1Descriptor = new MethodElement(new Fqsen('\MyClass::method1'));
        $strategyMock      = m::mock(ProjectFactoryStrategy::class);
        $containerMock     = m::mock(StrategyContainer::class);
        $classMock         = $this->buildClassMock();
        $classMock->shouldReceive('getDocComment')->andReturnNull();
        $classMock->stmts = [$method1];

        $strategyMock->shouldReceive('create')
            ->with($method1, $containerMock, null)
            ->andReturn($method1Descriptor);

        $containerMock->shouldReceive('findMatching')
            ->with($method1)
            ->andReturn($strategyMock);

        /** @var ClassDescriptor $class */
        $class = $this->fixture->create($classMock, $containerMock);

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
    public function testWithPropertyMembers() : void
    {
        $propertyProperty   = new PropertyProperty('\MyClass::$property');
        $property           = new PropertyNode(1, [$propertyProperty]);
        $propertyDescriptor = new PropertyElement(new Fqsen('\MyClass::$property'));
        $strategyMock       = m::mock(ProjectFactoryStrategy::class);
        $containerMock      = m::mock(StrategyContainer::class);
        $classMock          = $this->buildClassMock();
        $classMock->shouldReceive('getDocComment')->andReturnNull();
        $classMock->stmts = [$property];

        $strategyMock->shouldReceive('create')
            ->with(m::type(PropertyIterator::class), $containerMock, null)
            ->andReturn($propertyDescriptor);

        $containerMock->shouldReceive('findMatching')
            ->with(m::type(PropertyIterator::class))
            ->andReturn($strategyMock);

        /** @var ClassElement $class */
        $class = $this->fixture->create($classMock, $containerMock);

        $this->assertInstanceOf(ClassElement::class, $class);
        $this->assertEquals('\Space\MyClass', (string) $class->getFqsen());
        $this->assertEquals(
            ['\MyClass::$property' => $propertyDescriptor],
            $class->getProperties()
        );
    }

    /**
     * @covers ::create
     */
    public function testWithUsedTraits() : void
    {
        $trait         = new TraitUse([new Name('MyTrait'), new Name('OtherTrait')]);
        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching')->never();
        $classMock = $this->buildClassMock();
        $classMock->shouldReceive('getDocComment')->andReturnNull();
        $classMock->stmts = [$trait];

        /** @var ClassElement $class */
        $class = $this->fixture->create($classMock, $containerMock);

        $this->assertEquals(
            [
                '\MyTrait' => new Fqsen('\MyTrait'),
                '\OtherTrait' => new Fqsen('\OtherTrait'),
            ],
            $class->getUsedTraits()
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
     * @covers ::create
     */
    public function testCreateWithDocBlock() : void
    {
        $doc       = m::mock(Doc::class);
        $classMock = $this->buildClassMock();
        $classMock->shouldReceive('getDocComment')->andReturn($doc);

        $docBlock = new DocBlockElement('');

        $strategyMock  = m::mock(ProjectFactoryStrategy::class);
        $containerMock = m::mock(StrategyContainer::class);

        $strategyMock->shouldReceive('create')
            ->with($doc, $containerMock, null)
            ->andReturn($docBlock);

        $containerMock->shouldReceive('findMatching')
            ->with($doc)
            ->andReturn($strategyMock);

        /** @var ClassElement $class */
        $class = $this->fixture->create($classMock, $containerMock);

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

        return $classMock;
    }
}
