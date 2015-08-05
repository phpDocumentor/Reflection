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
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Stmt\Property as PropertyNode;
use PhpParser\Node\Stmt\Trait_ as TraitNode;
use phpDocumentor\Reflection\DocBlock as DocBlockElement;
use phpDocumentor\Reflection\Php\Trait_ as TraitElement;
use phpDocumentor\Reflection\Php\Property as PropertyElement;
use phpDocumentor\Reflection\Php\Method as MethodElement;
use PhpParser\Node\Stmt\TraitUse;

/**
 * Test case for Trait_
 * @coversDefaultClass phpDocumentor\Reflection\Php\Factory\Trait_
 * @covers ::<private>
 */
class Trait_Test extends TestCase
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
        $strategiesMock = m::mock(StrategyContainer::class);
        $interfaceMock = $this->buildTraitMock();
        $interfaceMock->shouldReceive('getDocComment')->andReturnNull();

        /** @var TraitElement $trait */
        $trait = $this->fixture->create($interfaceMock, $strategiesMock);

        $this->assertInstanceOf(TraitElement::class, $trait);
        $this->assertEquals('\Space\MyTrait', (string)$trait->getFqsen());
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

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching->create')
            ->once()
            ->with($doc, $containerMock, null)
            ->andReturn($docBlock);

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
        $property = new PropertyNode(1, [$propertyProperty] );
        $propertyDescriptor = new PropertyElement(new Fqsen('\Space\MyTrait::$property'));
        $strategiesMock = m::mock(StrategyContainer::class);
        $traitMock = $this->buildTraitMock();
        $traitMock->shouldReceive('getDocComment')->andReturnNull();
        $traitMock->stmts = [
            $property
        ];

        $strategiesMock->shouldReceive('findMatching->create')
            ->with(m::any(), $strategiesMock, null)
            ->andReturn($propertyDescriptor);

        /** @var TraitElement $trait */
        $trait = $this->fixture->create($traitMock, $strategiesMock);

        $this->assertInstanceOf(TraitElement::class, $trait);
        $this->assertEquals('\Space\MyTrait', (string)$trait->getFqsen());
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
        $strategiesMock = m::mock(StrategyContainer::class);
        $classMock = $this->buildTraitMock();
        $classMock->shouldReceive('getDocComment')->andReturnNull();
        $classMock->stmts = [
            $method1
        ];

        $strategiesMock->shouldReceive('findMatching->create')
            ->with($method1, $strategiesMock, null)
            ->andReturn($method1Descriptor);

        /** @var TraitElement $class */
        $class = $this->fixture->create($classMock, $strategiesMock);

        $this->assertInstanceOf(TraitElement::class, $class);
        $this->assertEquals('\Space\MyTrait', (string)$class->getFqsen());
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
        $strategiesMock = m::mock(StrategyContainer::class);
        $strategiesMock->shouldReceive('findMatching')->never();
        $traitMock = $this->buildTraitMock();
        $traitMock->shouldReceive('getDocComment')->andReturnNull();
        $traitMock->stmts = [
            $trait
        ];

        /** @var TraitElement $trait */
        $trait = $this->fixture->create($traitMock, $strategiesMock);

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
        $interfaceMock = m::mock(TraitNode::class);
        $interfaceMock->fqsen = new Fqsen('\Space\MyTrait');
        return $interfaceMock;
    }
}
