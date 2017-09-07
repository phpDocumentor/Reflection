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
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Comment\Doc;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Interface_ as InterfaceNode;
use phpDocumentor\Reflection\DocBlock as DocBlockElement;
use phpDocumentor\Reflection\Php\Constant as ConstantElement;
use phpDocumentor\Reflection\Php\Interface_ as InterfaceElement;
use phpDocumentor\Reflection\Php\Method as MethodElement;

/**
 * Test case for Interface_
 * @coversDefaultClass phpDocumentor\Reflection\Php\Factory\Interface_
 * @covers ::<!public>
 */
// @codingStandardsIgnoreStart
class Interface_Test extends TestCase
// @codingStandardsIgnoreEnd
{
    protected function setUp()
    {
        $this->fixture = new Interface_();
    }

    /**
     * @covers ::matches
     */
    public function testMatches()
    {
        $this->assertFalse($this->fixture->matches(new \stdClass()));
        $this->assertTrue($this->fixture->matches(m::mock(InterfaceNode::class)));
    }

    /**
     * @covers ::create
     */
    public function testSimpleCreate()
    {
        $strategiesMock = m::mock(StrategyContainer::class);
        $interfaceMock = $this->buildClassMock();
        $interfaceMock->shouldReceive('getDocComment')->andReturnNull();

        /** @var InterfaceElement $class */
        $class = $this->fixture->create($interfaceMock, $strategiesMock);

        $this->assertInstanceOf(InterfaceElement::class, $class);
        $this->assertEquals('\Space\MyInterface', (string)$class->getFqsen());
    }

    /**
     * @covers ::create
     */
    public function testCreateWithDocBlock()
    {
        $doc = m::mock(Doc::class);
        $interfaceMock = $this->buildClassMock();
        $interfaceMock->shouldReceive('getDocComment')->andReturn($doc);

        $docBlock = new DocBlockElement('');

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching->create')
            ->once()
            ->with($doc, $containerMock, null)
            ->andReturn($docBlock);

        /** @var InterfaceElement $interface */
        $interface = $this->fixture->create($interfaceMock, $containerMock);

        $this->assertSame($docBlock, $interface->getDocBlock());
    }

    /**
     * @covers ::create
     */
    public function testWithMethodMembers()
    {
        $method1 = new ClassMethod('\Space\MyInterface::method1');
        $method1Descriptor = new MethodElement(new Fqsen('\Space\MyInterface::method1'));
        $strategiesMock = m::mock(StrategyContainer::class);
        $interfaceMock = $this->buildClassMock();
        $interfaceMock->shouldReceive('getDocComment')->andReturnNull();
        $interfaceMock->stmts = [
            $method1
        ];

        $strategiesMock->shouldReceive('findMatching->create')
            ->with($method1, $strategiesMock, null)
            ->andReturn($method1Descriptor);

        $this->fixture->create($interfaceMock, $strategiesMock);

        /** @var InterfaceElement $interface */
        $interface = $this->fixture->create($interfaceMock, $strategiesMock);

        $this->assertInstanceOf(InterfaceElement::class, $interface);
        $this->assertEquals('\Space\MyInterface', (string)$interface->getFqsen());
        $this->assertEquals(
            ['\Space\MyInterface::method1' => $method1Descriptor],
            $interface->getMethods()
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
            $constant
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
     * @return m\MockInterface|InterfaceNode
     */
    private function buildClassMock()
    {
        $interfaceMock = m::mock(InterfaceNode::class);
        $interfaceMock->fqsen = new Fqsen('\Space\MyInterface');
        $interfaceMock->extends = [];
        $interfaceMock->shouldReceive('getLine')->andReturn(1);
        return $interfaceMock;
    }
}
