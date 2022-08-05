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

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Metadata\MetaDataContainer as MetaDataContainerInterface;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Trait_
 * @covers ::__construct
 * @covers ::<private>
 * @property Trait_ $fixture
 */
final class Trait_Test extends TestCase
{
    use MetadataContainerTest;

    private Fqsen $fqsen;

    private DocBlock $docBlock;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fqsen = new Fqsen('\MyTrait');
        $this->docBlock = new DocBlock('');
        $this->fixture = new Trait_($this->fqsen, $this->docBlock);
    }

    private function getFixture(): MetaDataContainerInterface
    {
        return $this->fixture;
    }

    /**
     * @covers ::getFqsen
     * @covers ::getName
     */
    public function testGetFqsenAndGetName(): void
    {
        $this->assertSame($this->fqsen, $this->fixture->getFqsen());
        $this->assertEquals($this->fqsen->getName(), $this->fixture->getName());
    }

    /**
     * @uses \phpDocumentor\Reflection\Php\Property
     * @uses \phpDocumentor\Reflection\Php\Visibility
     *
     * @covers ::addProperty
     * @covers ::getProperties
     */
    public function testAddAndGettingProperties(): void
    {
        $this->assertEquals([], $this->fixture->getProperties());

        $property = new Property(new Fqsen('\MyTrait::$myProperty'));

        $this->fixture->addProperty($property);

        $this->assertEquals(['\MyTrait::$myProperty' => $property], $this->fixture->getProperties());
    }

    /**
     * @uses \phpDocumentor\Reflection\Php\Method
     * @uses \phpDocumentor\Reflection\Php\Visibility
     *
     * @covers ::addMethod
     * @covers ::getMethods
     */
    public function testAddAndGettingMethods(): void
    {
        $this->assertEquals([], $this->fixture->getMethods());

        $method = new Method(new Fqsen('\MyTrait::myMethod()'));

        $this->fixture->addMethod($method);

        $this->assertEquals(['\MyTrait::myMethod()' => $method], $this->fixture->getMethods());
    }

    /**
     * @covers ::getUsedTraits
     * @covers ::AddUsedTrait
     */
    public function testAddAndGettingUsedTrait(): void
    {
        $this->assertEmpty($this->fixture->getUsedTraits());

        $trait = new Fqsen('\MyTrait');

        $this->fixture->addUsedTrait($trait);

        $this->assertSame(['\MyTrait' => $trait], $this->fixture->getUsedTraits());
    }

    /**
     * @covers ::getConstants
     * @covers ::addConstant
     */
    public function testAddAndGettingConstants(): void
    {
        $this->assertEmpty($this->fixture->getConstants());

        $constant = new Constant(new Fqsen('\MyClass::MY_CONSTANT'));

        $this->fixture->addConstant($constant);

        $this->assertSame(['\MyClass::MY_CONSTANT' => $constant], $this->fixture->getConstants());
    }

    /**
     * @covers ::getDocBlock
     */
    public function testGetDocblock(): void
    {
        $this->assertSame($this->docBlock, $this->fixture->getDocBlock());
    }

    public function testLineAndColumnNumberIsReturnedWhenALocationIsProvided(): void
    {
        $fixture = new Trait_($this->fqsen, $this->docBlock, new Location(100, 20), new Location(101, 20));
        $this->assertLineAndColumnNumberIsReturnedWhenALocationIsProvided($fixture);
    }
}
