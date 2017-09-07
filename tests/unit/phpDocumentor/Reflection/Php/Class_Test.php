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

namespace phpDocumentor\Reflection\Php;

use Mockery as m;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;

/**
 * Tests the functionality for the Class_ class.
 * @coversDefaultClass phpDocumentor\Reflection\Php\Class_
 */
// @codingStandardsIgnoreStart
class Class_Test extends \PHPUnit_Framework_TestCase
// @codingStandardsIgnoreEnd
{
    /**
     * @var Class_
     */
    private $fixture;

    /**
     * @var Fqsen
     */
    private $parent;

    /**
     * @var Fqsen
     */
    private $fqsen;

    /**
     * @var DocBlock
     */
    private $docBlock;
    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp()
    {
        $this->parent = new Fqsen('\MyParentClass');
        $this->fqsen = new Fqsen('\MyClass');
        $this->docBlock = new DocBlock('');

        $this->fixture = new Class_($this->fqsen, $this->docBlock, null, false, false, new Location(1));
    }

    /**
     * @covers ::getParent
     * @covers ::__construct
     */
    public function testGettingParent()
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
    public function testAddAndGettingInterfaces()
    {
        $this->assertEmpty($this->fixture->getInterfaces());

        $interface = new Fqsen('\MyInterface');

        $this->fixture->addInterface($interface);

        $this->assertSame(array('\MyInterface' => $interface), $this->fixture->getInterfaces());
    }

    /**
     * @covers ::getConstants
     * @covers ::addConstant
     */
    public function testAddAndGettingConstants()
    {
        $this->assertEmpty($this->fixture->getConstants());

        $constant = new Constant(new Fqsen('\MyClass::MY_CONSTANT'));

        $this->fixture->addConstant($constant);

        $this->assertSame(array('\MyClass::MY_CONSTANT' => $constant), $this->fixture->getConstants());
    }

    /**
     * @covers ::addProperty
     * @covers ::getProperties
     */
    public function testAddAndGettingProperties()
    {
        $this->assertEmpty($this->fixture->getProperties());

        $property = new Property(new Fqsen('\MyClass::$myProperty'));

        $this->fixture->addProperty($property);

        $this->assertSame(array('\MyClass::$myProperty' => $property), $this->fixture->getProperties());
    }

    /**
     * @covers ::addMethod
     * @covers ::getMethods
     */
    public function testAddAndGettingMethods()
    {
        $this->assertEmpty($this->fixture->getMethods());

        $method = new Method(new Fqsen('\MyClass::myMethod()'));

        $this->fixture->addMethod($method);

        $this->assertSame(array('\MyClass::myMethod()' => $method), $this->fixture->getMethods());
    }

    /**
     * @covers ::getUsedTraits
     * @covers ::AddUsedTrait
     */
    public function testAddAndGettingUsedTrait()
    {
        $this->assertEmpty($this->fixture->getUsedTraits());

        $trait = new Fqsen('\MyTrait');

        $this->fixture->addUsedTrait($trait);

        $this->assertSame(array('\MyTrait' => $trait), $this->fixture->getUsedTraits());
    }

    /**
     * @covers ::isAbstract
     * @covers ::__construct
     */
    public function testGettingWhetherClassIsAbstract()
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
    public function testGettingWhetherClassIsFinal()
    {
        $class = new Class_($this->fqsen, $this->docBlock, null, false, false);
        $this->assertFalse($class->isFinal());

        $class = new Class_($this->fqsen, $this->docBlock, null, false, true);
        $this->assertTrue($class->isFinal());
    }
}
