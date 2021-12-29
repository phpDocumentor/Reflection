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
 * @uses \phpDocumentor\Reflection\Php\Method
 * @uses \phpDocumentor\Reflection\Php\EnumCase
 *
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Enum_
 * @covers ::__construct
 * @covers ::<private>
 * @covers ::<protected>
 *
 * @property Enum_ $fixture
 */
final class Enum_Test extends TestCase
{
    use MetadataContainerTest;

    /** @var Fqsen */
    private $parent;

    /** @var Fqsen */
    private $fqsen;

    /** @var DocBlock */
    private $docBlock;

    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp(): void
    {
        $this->parent = new Fqsen('\MyParentEnum');
        $this->fqsen = new Fqsen('\Enum');
        $this->docBlock = new DocBlock('');

        $this->fixture = new Enum_($this->fqsen, null, $this->docBlock);
    }

    private function getFixture(): MetaDataContainerInterface
    {
        return $this->fixture;
    }

    /**
     * @covers ::getName
     */
    public function testGettingName(): void
    {
        $this->assertSame($this->fqsen->getName(), $this->fixture->getName());
    }

    /**
     * @covers ::getBackedType
     */
    public function testGetBackedWithOutType(): void
    {
        $this->assertNull($this->fixture->getBackedType());
    }

    /**
     * @covers ::getFqsen
     */
    public function testGettingFqsen(): void
    {
        $this->assertSame($this->fqsen, $this->fixture->getFqsen());
    }

    /**
     * @covers ::getDocBlock
     */
    public function testGettingDocBlock(): void
    {
        $this->assertSame($this->docBlock, $this->fixture->getDocBlock());
    }

    /**
     * @covers ::getInterfaces
     * @covers ::AddInterface
     */
    public function testAddAndGettingInterfaces(): void
    {
        $this->assertEmpty($this->fixture->getInterfaces());

        $interface = new Fqsen('\MyInterface');

        $this->fixture->addInterface($interface);

        $this->assertSame(['\MyInterface' => $interface], $this->fixture->getInterfaces());
    }

    /**
     * @covers ::addMethod
     * @covers ::getMethods
     */
    public function testAddAndGettingMethods(): void
    {
        $this->assertEmpty($this->fixture->getMethods());

        $method = new Method(new Fqsen('\MyClass::myMethod()'));

        $this->fixture->addMethod($method);

        $this->assertSame(['\MyClass::myMethod()' => $method], $this->fixture->getMethods());
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
     * @covers ::addCase
     * @covers ::getCases
     */
    public function testAddAndGettingCases(): void
    {
        $this->assertEmpty($this->fixture->getCases());

        $case = new EnumCase(new Fqsen('\MyEnum::VALUE'), null);

        $this->fixture->addCase($case);

        $this->assertSame(['\MyEnum::VALUE' => $case], $this->fixture->getCases());
    }

    public function testLineAndColumnNumberIsReturnedWhenALocationIsProvided(): void
    {
        $fixture = new Enum_($this->fqsen, null, $this->docBlock, new Location(100, 20), new Location(101, 20));
        $this->assertLineAndColumnNumberIsReturnedWhenALocationIsProvided($fixture);
    }
}
