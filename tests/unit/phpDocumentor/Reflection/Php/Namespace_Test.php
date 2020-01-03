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

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the Namespace_ class.
 *
 * @coversDefaultClass phpDocumentor\Reflection\Php\Namespace_
 */
// @codingStandardsIgnoreStart
class Namespace_Test extends TestCase
// @codingStandardsIgnoreEnd
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

        $class = new Fqsen('\MySpace\MyClass');
        $this->fixture->addClass($class);

        $this->assertEquals(['\MySpace\MyClass' => $class], $this->fixture->getClasses());
    }

    /**
     * @covers ::__construct
     * @covers ::getConstants
     * @covers ::addConstant
     */
    public function testAddAndGetConstants()
    {
        $this->assertEmpty($this->fixture->getConstants());

        $constant = new Fqsen('\MySpace::MY_CONSTANT');
        $this->fixture->addConstant($constant);

        $this->assertEquals(['\MySpace::MY_CONSTANT' => $constant], $this->fixture->getConstants());
    }

    /**
     * @covers ::__construct
     * @covers ::getFunctions
     * @covers ::addFunction
     */
    public function testAddAndGetFunctions()
    {
        $this->assertEmpty($this->fixture->getFunctions());

        $function = new Fqsen('\MySpace\MyFunction()');
        $this->fixture->addFunction($function);

        $this->assertEquals(['\MySpace\MyFunction()' => $function], $this->fixture->getFunctions());
    }

    /**
     * @covers ::__construct
     * @covers ::getInterfaces
     * @covers ::addInterface
     */
    public function testAddAndGetInterfaces()
    {
        $this->assertEmpty($this->fixture->getInterfaces());

        $interface = new Fqsen('\MySpace\MyInterface');
        $this->fixture->addInterface($interface);

        $this->assertEquals(['\MySpace\MyInterface' => $interface], $this->fixture->getInterfaces());
    }

    /**
     * @covers ::__construct
     * @covers ::getTraits
     * @covers ::addTrait
     */
    public function testAddAndGetTraits()
    {
        $this->assertEmpty($this->fixture->getTraits());

        $trait = new Fqsen('\MySpace\MyTrait');
        $this->fixture->addTrait($trait);

        $this->assertEquals(['\MySpace\MyTrait' => $trait], $this->fixture->getTraits());
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
}
