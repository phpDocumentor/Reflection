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
use PHPUnit\Framework\TestCase;

/**
 * @uses \phpDocumentor\Reflection\Php\Property
 * @uses \phpDocumentor\Reflection\Php\Constant
 * @uses \phpDocumentor\Reflection\Php\Method
 * @uses \phpDocumentor\Reflection\Php\Visibility
 *
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Class_
 * @covers ::__construct
 * @covers ::<private>
 * @covers ::<protected>
 */
final class Class_Test extends TestCase
{
    /** @var Class_ */
    private $fixture;

    /** @var Fqsen */
    private $parent;

    /** @var Fqsen */
    private $fqsen;

    /** @var DocBlock */
    private $docBlock;

    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp() : void
    {
        $this->parent = new Fqsen('\MyParentClass');
        $this->fqsen = new Fqsen('\MyClass');
        $this->docBlock = new DocBlock('');

        $this->fixture = new Class_($this->fqsen, $this->docBlock);
    }

    /**
     * @covers ::getName
     */
    public function testGettingName() : void
    {
        $this->assertSame($this->fqsen->getName(), $this->fixture->getName());
    }

    /**
     * @covers ::getFqsen
     */
    public function testGettingFqsen() : void
    {
        $this->assertSame($this->fqsen, $this->fixture->getFqsen());
    }

    /**
     * @covers ::getDocBlock
     */
    public function testGettingDocBlock() : void
    {
        $this->assertSame($this->docBlock, $this->fixture->getDocBlock());
    }

    /**
     * @covers ::getParent
     */
    public function testGettingParent() : void
    {
        $class = new Class_($this->fqsen, $this->docBlock);
        $this->assertNull($class->getParent());

        $class = new Class_($this->fqsen, $this->docBlock, $this->parent);
        $this->assertSame($this->parent, $class->getParent());
    }

    /**
     * @covers ::getInterfaces
     * @covers ::AddInterface
     */
    public function testAddAndGettingInterfaces() : void
    {
        $this->assertEmpty($this->fixture->getInterfaces());

        $interface = new Fqsen('\MyInterface');

        $this->fixture->addInterface($interface);

        $this->assertSame(['\MyInterface' => $interface], $this->fixture->getInterfaces());
    }

    /**
     * @covers ::getConstants
     * @covers ::addConstant
     */
    public function testAddAndGettingConstants() : void
    {
        $this->assertEmpty($this->fixture->getConstants());

        $constant = new Constant(new Fqsen('\MyClass::MY_CONSTANT'));

        $this->fixture->addConstant($constant);

        $this->assertSame(['\MyClass::MY_CONSTANT' => $constant], $this->fixture->getConstants());
    }

    /**
     * @covers ::addProperty
     * @covers ::getProperties
     */
    public function testAddAndGettingProperties() : void
    {
        $this->assertEmpty($this->fixture->getProperties());

        $property = new Property(new Fqsen('\MyClass::$myProperty'));

        $this->fixture->addProperty($property);

        $this->assertSame(['\MyClass::$myProperty' => $property], $this->fixture->getProperties());
    }

    /**
     * @covers ::addMethod
     * @covers ::getMethods
     */
    public function testAddAndGettingMethods() : void
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
    public function testAddAndGettingUsedTrait() : void
    {
        $this->assertEmpty($this->fixture->getUsedTraits());

        $trait = new Fqsen('\MyTrait');

        $this->fixture->addUsedTrait($trait);

        $this->assertSame(['\MyTrait' => $trait], $this->fixture->getUsedTraits());
    }

    /**
     * @covers ::isAbstract
     */
    public function testGettingWhetherClassIsAbstract() : void
    {
        $class = new Class_($this->fqsen, $this->docBlock);
        $this->assertFalse($class->isAbstract());

        $class = new Class_($this->fqsen, $this->docBlock, null, true);
        $this->assertTrue($class->isAbstract());
    }

    /**
     * @covers ::isFinal
     */
    public function testGettingWhetherClassIsFinal() : void
    {
        $class = new Class_($this->fqsen, $this->docBlock);
        $this->assertFalse($class->isFinal());

        $class = new Class_($this->fqsen, $this->docBlock, null, false, true);
        $this->assertTrue($class->isFinal());
    }

    /**
     * @covers ::getLocation
     */
    public function testLineNumberIsMinusOneWhenNoneIsProvided() : void
    {
        $this->assertSame(-1, $this->fixture->getLocation()->getLineNumber());
        $this->assertSame(0, $this->fixture->getLocation()->getColumnNumber());
    }

    /**
     * @uses \phpDocumentor\Reflection\Location
     *
     * @covers ::getLocation
     */
    public function testLineAndColumnNumberIsReturnedWhenALocationIsProvided() : void
    {
        $fixture = new Class_($this->fqsen, $this->docBlock, null, false, false, new Location(100, 20));

        $this->assertSame(100, $fixture->getLocation()->getLineNumber());
        $this->assertSame(20, $fixture->getLocation()->getColumnNumber());
    }
}
