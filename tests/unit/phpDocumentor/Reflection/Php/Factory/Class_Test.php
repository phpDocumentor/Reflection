<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Factory;

use Mockery as m;
use phpDocumentor\Reflection\DocBlock as DocBlockElement;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Class_ as ClassElement;
use phpDocumentor\Reflection\Php\Constant as ConstantElement;
use phpDocumentor\Reflection\Php\Method as MethodElement;
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

/**
 * Class Class_Test
 * @coversDefaultClass phpDocumentor\Reflection\Php\Factory\Class_
 * @covers ::<!public>
 */
// @codingStandardsIgnoreStart
class Class_Test extends TestCase
// @codingStandardsIgnoreEnd
{
    protected function setUp()
    {
        $this->fixture = new Class_();
    }

    /**
     * @covers ::matches
     */
    public function testMatches()
    {
        $this->assertFalse($this->fixture->matches(new \stdClass()));
        $this->assertTrue($this->fixture->matches(m::mock(ClassNode::class)));
    }

    /**
     * @covers ::create
     */
    public function testSimpleCreate()
    {
        $strategiesMock = m::mock(StrategyContainer::class);
        $classMock = $this->buildClassMock();
        $classMock->shouldReceive('getDocComment')->andReturnNull();

        /** @var ClassElement $class */
        $class = $this->fixture->create($classMock, $strategiesMock);

        $this->assertInstanceOf(ClassElement::class, $class);
        $this->assertEquals('\Space\MyClass', (string) $class->getFqsen());
        $this->assertNull($class->getParent());
        $this->assertTrue($class->isFinal());
        $this->assertTrue($class->isAbstract());
    }

    /**
     * @covers ::create
     */
    public function testClassWithParent()
    {
        $strategiesMock = m::mock(StrategyContainer::class);
        $classMock = $this->buildClassMock();
        $classMock->shouldReceive('getDocComment')->andReturnNull();
        $classMock->extends = 'Space\MyParent';

        /** @var ClassElement $class */
        $class = $this->fixture->create($classMock, $strategiesMock);

        $this->assertInstanceOf(ClassElement::class, $class);
        $this->assertEquals('\Space\MyClass', (string) $class->getFqsen());
        $this->assertEquals('\Space\MyParent', (string) $class->getParent());
    }

    /**
     * @covers ::create
     */
    public function testClassImplementingInterface()
    {
        $strategiesMock = m::mock(StrategyContainer::class);
        $classMock = $this->buildClassMock();
        $classMock->shouldReceive('getDocComment')->andReturnNull();
        $classMock->extends = 'Space\MyParent';
        $classMock->implements = [
            new Name('MyInterface'),
        ];

        /** @var ClassElement $class */
        $class = $this->fixture->create($classMock, $strategiesMock);

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
    public function testWithMethodMembers()
    {
        $method1 = new ClassMethod('MyClass::method1');
        $method1Descriptor = new MethodElement(new Fqsen('\MyClass::method1'));
        $strategiesMock = m::mock(StrategyContainer::class);
        $classMock = $this->buildClassMock();
        $classMock->shouldReceive('getDocComment')->andReturnNull();
        $classMock->stmts = [
            $method1,
        ];

        $strategiesMock->shouldReceive('findMatching->create')
            ->with($method1, $strategiesMock, null)
            ->andReturn($method1Descriptor);

        /** @var ClassDescriptor $class */
        $class = $this->fixture->create($classMock, $strategiesMock);

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
    public function testWithPropertyMembers()
    {
        $propertyProperty = new PropertyProperty('\MyClass::$property');
        $property = new PropertyNode(1, [$propertyProperty]);
        $propertyDescriptor = new PropertyElement(new Fqsen('\MyClass::$property'));
        $strategiesMock = m::mock(StrategyContainer::class);
        $classMock = $this->buildClassMock();
        $classMock->shouldReceive('getDocComment')->andReturnNull();
        $classMock->stmts = [
            $property,
        ];

        $strategiesMock->shouldReceive('findMatching->create')
            ->with(m::any(), $strategiesMock, null)
            ->andReturn($propertyDescriptor);

        /** @var ClassElement $class */
        $class = $this->fixture->create($classMock, $strategiesMock);

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
    public function testWithUsedTraits()
    {
        $trait = new TraitUse([new Name('MyTrait'), new Name('OtherTrait')]);
        $strategiesMock = m::mock(StrategyContainer::class);
        $strategiesMock->shouldReceive('findMatching')->never();
        $classMock = $this->buildClassMock();
        $classMock->shouldReceive('getDocComment')->andReturnNull();
        $classMock->stmts = [
            $trait,
        ];

        /** @var ClassElement $class */
        $class = $this->fixture->create($classMock, $strategiesMock);

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
    public function testWithConstants()
    {
        $const = new Const_('\Space\MyClass::MY_CONST', new Variable('a'));
        $constant = new ClassConst([$const]);

        $result = new ConstantElement(new Fqsen('\Space\MyClass::MY_CONST'));
        $strategiesMock = m::mock(StrategyContainer::class);
        $strategiesMock->shouldReceive('findMatching->create')
            ->with(m::type(ClassConstantIterator::class), $strategiesMock, null)
            ->andReturn($result);
        $classMock = $this->buildClassMock();
        $classMock->shouldReceive('getDocComment')->andReturnNull();
        $classMock->stmts = [
            $constant,
        ];

        /** @var ClassElement $class */
        $class = $this->fixture->create($classMock, $strategiesMock);

        $this->assertEquals(
            [
                '\Space\MyClass::MY_CONST' => $result,
            ],
            $class->getConstants()
        );
    }

    /**
     * @covers ::create
     */
    public function testCreateWithDocBlock()
    {
        $doc = m::mock(Doc::class);
        $classMock = $this->buildClassMock();
        $classMock->shouldReceive('getDocComment')->andReturn($doc);

        $docBlock = new DocBlockElement('');

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching->create')
            ->once()
            ->with($doc, $containerMock, null)
            ->andReturn($docBlock);

        /** @var ClassElement $class */
        $class = $this->fixture->create($classMock, $containerMock);

        $this->assertSame($docBlock, $class->getDocBlock());
    }

    /**
     * @return m\MockInterface|ClassNode
     */
    private function buildClassMock()
    {
        $classMock = m::mock(ClassNode::class);
        $classMock->fqsen = new Fqsen('\Space\MyClass');
        $classMock->shouldReceive('isFinal')->andReturn(true);
        $classMock->shouldReceive('isAbstract')->andReturn(true);
        $classMock->shouldReceive('getLine')->andReturn(1);
        return $classMock;
    }
}
