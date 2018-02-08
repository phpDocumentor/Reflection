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
use phpDocumentor\Reflection\Php\Method as MethodElement;
use phpDocumentor\Reflection\Php\Property as PropertyElement;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Php\Trait_ as TraitElement;
use PhpParser\Comment\Doc;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property as PropertyNode;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Stmt\Trait_ as TraitNode;
use PhpParser\Node\Stmt\TraitUse;

/**
 * Test case for Trait_
 * @coversDefaultClass phpDocumentor\Reflection\Php\Factory\Trait_
 * @covers ::<!public>
 */
// @codingStandardsIgnoreStart
class Trait_Test extends TestCase
// @codingStandardsIgnoreEnd
{
    protected function setUp()
    {
        $this->fixture = new Trait_();
    }

    /**
     * @covers ::matches
     */
    public function testMatches()
    {
        $this->assertFalse($this->fixture->matches(new \stdClass()));
        $this->assertTrue($this->fixture->matches(m::mock(TraitNode::class)));
    }

    /**
     * @covers ::create
     */
    public function testSimpleCreate()
    {
        $containerMock = m::mock(StrategyContainer::class);
        $interfaceMock = $this->buildTraitMock();
        $interfaceMock->shouldReceive('getDocComment')->andReturnNull();

        /** @var TraitElement $trait */
        $trait = $this->fixture->create($interfaceMock, $containerMock);

        $this->assertInstanceOf(TraitElement::class, $trait);
        $this->assertEquals('\Space\MyTrait', (string) $trait->getFqsen());
    }

    /**
     * @covers ::create
     */
    public function testCreateWithDocBlock()
    {
        $doc = m::mock(Doc::class);
        $interfaceMock = $this->buildTraitMock();
        $interfaceMock->shouldReceive('getDocComment')->andReturn($doc);

        $docBlock = new DocBlockElement('');

        $strategyMock = m::mock(ProjectFactoryStrategy::class);
        $containerMock = m::mock(StrategyContainer::class);

        $strategyMock->shouldReceive('create')
            ->with($doc, $containerMock, null)
            ->andReturn($docBlock);

        $containerMock->shouldReceive('findMatching')
            ->with($doc)
            ->andReturn($strategyMock);

        /** @var TraitElement $trait */
        $trait = $this->fixture->create($interfaceMock, $containerMock);

        $this->assertSame($docBlock, $trait->getDocBlock());
    }

    /**
     * @covers ::create
     */
    public function testWithPropertyMembers()
    {
        $propertyProperty = new PropertyProperty('\Space\MyTrait::$property');
        $property = new PropertyNode(1, [$propertyProperty]);
        $propertyDescriptor = new PropertyElement(new Fqsen('\Space\MyTrait::$property'));
        $strategyMock = m::mock(ProjectFactoryStrategy::class);
        $containerMock = m::mock(StrategyContainer::class);
        $traitMock = $this->buildTraitMock();
        $traitMock->shouldReceive('getDocComment')->andReturnNull();
        $traitMock->stmts = [
            $property,
        ];

        $strategyMock->shouldReceive('create')
            ->with(m::type(PropertyIterator::class), $containerMock, null)
            ->andReturn($propertyDescriptor);

        $containerMock->shouldReceive('findMatching')
            ->with(m::type(PropertyIterator::class))
            ->andReturn($strategyMock);

        /** @var TraitElement $trait */
        $trait = $this->fixture->create($traitMock, $containerMock);

        $this->assertInstanceOf(TraitElement::class, $trait);
        $this->assertEquals('\Space\MyTrait', (string) $trait->getFqsen());
        $this->assertEquals(
            ['\Space\MyTrait::$property' => $propertyDescriptor],
            $trait->getProperties()
        );
    }

    /**
     * @covers ::create
     */
    public function testWithMethodMembers()
    {
        $method1 = new ClassMethod('MyTrait::method1');
        $method1Descriptor = new MethodElement(new Fqsen('\MyTrait::method1'));
        $strategyMock = m::mock(ProjectFactoryStrategy::class);
        $containerMock = m::mock(StrategyContainer::class);
        $traitMock = $this->buildTraitMock();
        $traitMock->shouldReceive('getDocComment')->andReturnNull();
        $traitMock->stmts = [
            $method1,
        ];

        $strategyMock->shouldReceive('create')
            ->with($method1, $containerMock, null)
            ->andReturn($method1Descriptor);

        $containerMock->shouldReceive('findMatching')
            ->with($method1)
            ->andReturn($strategyMock);

        /** @var TraitElement $class */
        $class = $this->fixture->create($traitMock, $containerMock);

        $this->assertInstanceOf(TraitElement::class, $class);
        $this->assertEquals('\Space\MyTrait', (string) $class->getFqsen());
        $this->assertEquals(
            ['\MyTrait::method1' => $method1Descriptor],
            $class->getMethods()
        );
    }

    /**
     * @covers ::create
     */
    public function testWithUsedTraits()
    {
        $trait = new TraitUse([new Name('MyTrait')]);
        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching')->never();
        $traitMock = $this->buildTraitMock();
        $traitMock->shouldReceive('getDocComment')->andReturnNull();
        $traitMock->stmts = [
            $trait,
        ];

        /** @var TraitElement $trait */
        $trait = $this->fixture->create($traitMock, $containerMock);

        $this->assertEquals(
            [
                '\MyTrait' => new Fqsen('\MyTrait'),
            ],
            $trait->getUsedTraits()
        );
    }

    /**
     * @return m\MockInterface|TraitNode
     */
    private function buildTraitMock()
    {
        $mock = m::mock(TraitNode::class);
        $mock->fqsen = new Fqsen('\Space\MyTrait');
        $mock->shouldReceive('getLine')->andReturn(1);
        return $mock;
    }
}
