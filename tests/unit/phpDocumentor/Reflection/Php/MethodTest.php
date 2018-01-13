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

use \Mockery as m;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the Method class.
 * @coversDefaultClass phpDocumentor\Reflection\Php\Method
 */
class MethodTest extends TestCase
{
    /** @var Method $fixture */
    protected $fixture;

    private $fqsen;

    private $visibility;

    private $docblock;

    protected function setUp()
    {
        $this->fqsen = new Fqsen('\My\Space::MyMethod()');
        $this->visibility = new Visibility('private');
        $this->docblock = new DocBlock('');
    }

    protected function tearDown()
    {
        m::close();
    }

    /**
     * @covers ::getFqsen
     * @covers ::getName
     * @covers ::__construct
     */
    public function testGetFqsenAndGetName()
    {
        $method = new Method($this->fqsen);

        $this->assertSame($this->fqsen, $method->getFqsen());
        $this->assertEquals($this->fqsen->getName(), $method->getName());
    }

    /**
     * @covers ::getDocblock
     * @covers ::__construct
     */
    public function testGetDocBlock()
    {
        $method = new Method($this->fqsen, $this->visibility, $this->docblock);

        $this->assertSame($this->docblock, $method->getDocBlock());
    }

    /**
     * @covers ::getArguments
     * @covers ::addArgument
     */
    public function testAddingAndGettingArguments()
    {
        $method = new Method($this->fqsen);
        $this->assertEquals([], $method->getArguments());

        $argument = new Argument('myArgument');
        $method->addArgument($argument);

        $this->assertEquals([$argument], $method->getArguments());
    }

    /**
     * @covers ::isAbstract
     * @covers ::__construct
     */
    public function testGettingWhetherMethodIsAbstract()
    {
        $method = new Method($this->fqsen, $this->visibility, $this->docblock, false);
        $this->assertFalse($method->isAbstract());

        $method = new Method($this->fqsen, $this->visibility, $this->docblock, true);
        $this->assertTrue($method->isAbstract());
    }

    /**
     * @covers ::isFinal
     * @covers ::__construct
     */
    public function testGettingWhetherMethodIsFinal()
    {
        $method = new Method($this->fqsen, $this->visibility, $this->docblock, false, false, false);
        $this->assertFalse($method->isFinal());

        $method = new Method($this->fqsen, $this->visibility, $this->docblock, false, false, true);
        $this->assertTrue($method->isFinal());
    }

    /**
     * @covers ::isStatic
     * @covers ::__construct
     */
    public function testGettingWhetherMethodIsStatic()
    {
        $method = new Method($this->fqsen, $this->visibility, $this->docblock, false, false, false);
        $this->assertFalse($method->isStatic());

        $method = new Method($this->fqsen, $this->visibility, $this->docblock, false, true, false);
        $this->assertTrue($method->isStatic());
    }

    /**
     * @covers ::getVisibility
     * @covers ::__construct
     */
    public function testGettingVisibility()
    {
        $method = new Method($this->fqsen, $this->visibility, $this->docblock, false, false, false);
        $this->assertSame($this->visibility, $method->getVisibility());
    }

    /**
     * @covers ::getVisibility
     * @covers ::__construct
     */
    public function testGetDefaultVisibility()
    {
        $method = new Method($this->fqsen);
        $this->assertEquals(new Visibility('public'), $method->getVisibility());
    }

    /**
     * @covers ::getReturnType
     * @covers ::__construct
     */
    public function testGetDefaultReturnType()
    {
        $method = new Method($this->fqsen);
        $this->assertEquals(new Mixed_(), $method->getReturnType());
    }

    /**
     * @covers ::getReturnType
     * @covers ::__construct
     */
    public function testGetReturnTypeFromConstructor()
    {
        $returnType = new String_();
        $method = new Method(
            $this->fqsen,
            new Visibility('public'),
            null,
            false,
            false,
            false,
            null,
            $returnType
        );

        $this->assertSame($returnType, $method->getReturnType());
    }
}
