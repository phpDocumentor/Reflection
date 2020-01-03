<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php;

use Mockery as m;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the Class_ class.
 *
 * @coversDefaultClass phpDocumentor\Reflection\Php\Class_
 */
// @codingStandardsIgnoreStart
class Class_Test extends TestCase
// @codingStandardsIgnoreEnd
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
        $this->parent   = new Fqsen('\MyParentClass');
        $this->fqsen    = new Fqsen('\MyClass');
        $this->docBlock = new DocBlock('');

        $this->fixture = new Class_($this->fqsen, $this->docBlock, null, false, false, new Location(1));
    }

    protected function tearDown() : void
    {
        m::close();
    }

    /**
     * @covers ::getParent
     * @covers ::__construct
     */
    public function testGettingParent() : void
    {
        $class = new Class_($this->fqsen, $this->docBlock, null, false, false, null);
        $this->assertNull($class->getParent());

        $class = new Class_($this->fqsen, $this->docBlock, $this->parent, false, false, null);
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
     * @covers ::__construct
     */
    public function testGettingWhetherClassIsAbstract() : void
    {
        $class = new Class_($this->fqsen, $this->docBlock, null, false, false);
        $this->assertFalse($class->isAbstract());

        $class = new Class_($this->fqsen, $this->docBlock, null, true, false);
        $this->assertTrue($class->isAbstract());
    }

    /**
     * @covers ::isFinal
     * @covers ::__construct
     */
    public function testGettingWhetherClassIsFinal() : void
    {
        $class = new Class_($this->fqsen, $this->docBlock, null, false, false);
        $this->assertFalse($class->isFinal());

        $class = new Class_($this->fqsen, $this->docBlock, null, false, true);
        $this->assertTrue($class->isFinal());
    }
}
