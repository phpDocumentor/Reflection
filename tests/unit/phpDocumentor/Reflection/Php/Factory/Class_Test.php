<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */


namespace phpDocumentor\Reflection\Php\Factory;

use Mockery as m;
use phpDocumentor\Descriptor\Method;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_ as ClassNode;
use phpDocumentor\Descriptor\Class_ as ClassDescriptor;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\PropertyProperty;

/**
 * Class Class_Test
 * @coversDefaultClass phpDocumentor\Reflection\Php\Factory\Class_
 * @covers ::<private>
 */
class Class_Test extends TestCase
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

        /** @var ClassDescriptor $class */
        $class = $this->fixture->create($classMock, $strategiesMock);

        $this->assertInstanceOf(ClassDescriptor::class, $class);
        $this->assertEquals('\Space\MyClass', (string)$class->getFqsen());
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
        $classMock->extends = 'Space\MyParent';

        /** @var ClassDescriptor $class */
        $class = $this->fixture->create($classMock, $strategiesMock);

        $this->assertInstanceOf(ClassDescriptor::class, $class);
        $this->assertEquals('\Space\MyClass', (string)$class->getFqsen());
        $this->assertEquals('\Space\MyParent', (string)$class->getParent());
    }

    /**
     * @covers ::create
     */
    public function testClassImplementingInterface()
    {
        $strategiesMock = m::mock(StrategyContainer::class);
        $classMock = $this->buildClassMock();
        $classMock->extends = 'Space\MyParent';
        $classMock->implements = [
            new Name('MyInterface')
        ];

        /** @var ClassDescriptor $class */
        $class = $this->fixture->create($classMock, $strategiesMock);

        $this->assertInstanceOf(ClassDescriptor::class, $class);
        $this->assertEquals('\Space\MyClass', (string)$class->getFqsen());

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
        $method1Descriptor = new Method(new Fqsen('\MyClass::method1'));
        $strategiesMock = m::mock(StrategyContainer::class);
        $classMock = $this->buildClassMock();
        $classMock->stmts = [
            $method1
        ];

        $strategiesMock->shouldReceive('findMatching->create')->with($method1, $strategiesMock)->andReturn($method1Descriptor);

        $this->fixture->create($classMock, $strategiesMock);

        /** @var ClassDescriptor $class */
        $class = $this->fixture->create($classMock, $strategiesMock);

        $this->assertInstanceOf(ClassDescriptor::class, $class);
        $this->assertEquals('\Space\MyClass', (string)$class->getFqsen());
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
        $property = new Property(1, [$propertyProperty] );
        $propertyDescriptor = new \phpDocumentor\Descriptor\Property(new Fqsen('\MyClass::$property'));
        $strategiesMock = m::mock(StrategyContainer::class);
        $classMock = $this->buildClassMock();
        $classMock->stmts = [
            $property
        ];

        $strategiesMock->shouldReceive('findMatching->create')->with(m::any(), $strategiesMock)->andReturn($propertyDescriptor);

        $this->fixture->create($classMock, $strategiesMock);

        /** @var ClassDescriptor $class */
        $class = $this->fixture->create($classMock, $strategiesMock);

        $this->assertInstanceOf(ClassDescriptor::class, $class);
        $this->assertEquals('\Space\MyClass', (string)$class->getFqsen());
        $this->assertEquals(
            ['\MyClass::$property' => $propertyDescriptor],
            $class->getProperties()
        );
    }


    /**
     * @return m\MockInterface|ClassNode
     */
    private function buildClassMock()
    {
        $classMock = m::mock(ClassNode::class);
        $classMock->name = 'Space\MyClass';
        $classMock->shouldReceive('isFinal')->andReturn(true);
        $classMock->shouldReceive('isAbstract')->andReturn(true);
        return $classMock;
    }
}
