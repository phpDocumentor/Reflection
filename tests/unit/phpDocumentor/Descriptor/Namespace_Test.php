<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\DocBlock;

/**
 * Tests the functionality for the Namespace_ class.
 *
 * @coversDefaultClass phpDocumentor\Descriptor\Namespace_
 */
class Namespace_Test extends \PHPUnit_Framework_TestCase
{
    /** @var Namespace_ $fixture */
    protected $fixture;

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
        $this->fqsen = new Fqsen('\MySpace');
        $this->docBlock = new DocBlock('');

        $this->fixture = new Namespace_($this->fqsen, $this->docBlock);
    }

    /**
     * @covers ::__construct
     * @covers ::getClasses
     * @covers ::AddClass
     */
    public function testAddAndGetClasses()
    {
        $this->assertEmpty($this->fixture->getClasses());

        $class = new Class_(new Fqsen('\MySpace\MyClass'));
        $this->fixture->addClass($class);

        $this->assertEquals(array('\MySpace\MyClass' => $class), $this->fixture->getClasses());
    }

    /**
     * @covers ::__construct
     * @covers ::getConstants
     * @covers ::addConstant
     */
    public function testAddAndGetConstants()
    {
        $this->assertEmpty($this->fixture->getConstants());

        $constant = new Constant(new Fqsen('\MySpace::MY_CONSTANT'));
        $this->fixture->addConstant($constant);

        $this->assertEquals(array('\MySpace::MY_CONSTANT' => $constant), $this->fixture->getConstants());
    }

    /**
     * @covers ::__construct
     * @covers ::getFunctions
     * @covers ::addFunction
     */
    public function testAddAndGetFunctions()
    {
        $this->assertEmpty($this->fixture->getFunctions());

        $function = new Function_(new Fqsen('\MySpace::MyFunction()'));
        $this->fixture->addFunction($function);

        $this->assertEquals(array('\MySpace::MyFunction()' => $function), $this->fixture->getFunctions());
    }

    /**
     * @covers ::__construct
     * @covers ::getInterfaces
     * @covers ::addInterface
     */
    public function testAddAndGetInterfaces()
    {
        $this->assertEmpty($this->fixture->getInterfaces());

        $interface = new Interface_(new Fqsen('\MySpace\MyInterface'));
        $this->fixture->addInterface($interface);

        $this->assertEquals(array('\MySpace\MyInterface' => $interface), $this->fixture->getInterfaces());
    }

    /**
     * @covers ::__construct
     * @covers ::getChildren
     * @covers ::AddChild
     */
    public function testAddAndGetChildren()
    {
        $this->assertEmpty($this->fixture->getChildren());

        $namespace = new Namespace_(new Fqsen('\MySpace\MySubSpace'));
        $this->fixture->addChild($namespace);

        $this->assertEquals(array('\MySpace\MySubSpace' => $namespace), $this->fixture->getChildren());
    }

    /**
     * @covers ::__construct
     * @covers ::getTraits
     * @covers ::addTrait
     */
    public function testAddAndGetTraits()
    {
        $this->assertEmpty($this->fixture->getTraits());

        $trait = new Trait_(new Fqsen('\MySpace\MyTrait'));
        $this->fixture->addTrait($trait);

        $this->assertEquals(array('\MySpace\MyTrait' => $trait), $this->fixture->getTraits());
    }

    /**
     * @covers ::__construct
     * @covers ::getFqsen
     * @covers ::getName
     */
    public function testGetFqsen()
    {
        $this->assertSame($this->fqsen, $this->fixture->getFqsen());
        $this->assertEquals($this->fqsen->getName(), $this->fixture->getName());
    }

    /**
     * @covers ::__construct
     * @covers ::getDocBlock
     */
    public function testGetDocblock()
    {
        $this->assertSame($this->docBlock, $this->fixture->getDocBlock());
    }
}
