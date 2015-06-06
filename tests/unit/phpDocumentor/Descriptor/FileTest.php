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

use \Mockery as m;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;

/**
 * Tests the functionality for the File class.
 *
 * @coversDefaultClass phpDocumentor\Descriptor\File
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    const EXAMPLE_HASH   = 'a-hash-string';
    const EXAMPLE_PATH   = 'a-path-string';
    const EXAMPLE_SOURCE = 'a-source-string';

    /** @var File $fixture */
    protected $fixture;

    /**
     * @var DocBlock
     */
    private $docBlock;

    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp()
    {
        $this->docBlock = new DocBlock('');

        $this->fixture = new File(static::EXAMPLE_HASH, static::EXAMPLE_PATH, static::EXAMPLE_SOURCE, $this->docBlock);
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
     * @covers ::getDocBlock
     */
    public function testGetDocBlock()
    {
        $this->assertSame($this->docBlock, $this->fixture->getDocBlock());
    }

    /**
     * @covers ::__construct
     * @covers ::getHash
     */
    public function testGetHash()
    {
        $this->assertSame(self::EXAMPLE_HASH, $this->fixture->getHash());
    }

    /**
     * @covers ::getPath
     */
    public function testSetAndGetPath()
    {
        $this->assertSame(self::EXAMPLE_PATH, $this->fixture->getPath());
    }

    /**
     * @covers ::getSource
     */
    public function testSetAndGetSource()
    {
        $this->assertSame(self::EXAMPLE_SOURCE, $this->fixture->getSource());
    }

    /**
     * @covers ::setNamespaceAliases
     * @covers ::getNamespaceAliases
     */
    public function testSetAndGetNamespaceAliases()
    {
        $this->markTestSkipped('todo');
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getNamespaceAliases());

        $mockInstance = m::mock('phpDocumentor\Descriptor\Collection');
        $mock = $mockInstance;

        $this->fixture->setNamespaceAliases($mock);

        $this->assertSame($mockInstance, $this->fixture->getNamespaceAliases());
    }

    /**
     * @covers ::getIncludes
     * @covers ::addInclude
     */
    public function testAddAndGetIncludes()
    {
        $this->assertEmpty($this->fixture->getIncludes());

        $include = static::EXAMPLE_PATH;
        $this->fixture->addInclude($include);

        $this->assertSame(array(static::EXAMPLE_PATH => static::EXAMPLE_PATH), $this->fixture->getIncludes());
    }
}
