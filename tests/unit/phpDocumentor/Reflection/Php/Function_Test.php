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
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the Function_ class.
 *
 * @coversDefaultClass phpDocumentor\Reflection\Php\Function_
 */
// @codingStandardsIgnoreStart
class Function_Test extends TestCase
// @codingStandardsIgnoreEnd
{
    /** @var Function_ $fixture */
    protected $fixture;

    /** @var Fqsen */
    protected $fqsen;

    /** @var DocBlock */
    protected $docBlock;

    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp() : void
    {
        $this->fqsen    = new Fqsen('\space\MyFunction()');
        $this->docBlock = new DocBlock('aa');
        $this->fixture  = new Function_($this->fqsen, $this->docBlock);
    }

    protected function tearDown() : void
    {
        m::close();
    }

    /**
     * @covers ::__construct
     * @covers ::getName
     */
    public function testGetName() : void
    {
        $this->assertEquals('MyFunction', $this->fixture->getName());
    }

    /**
     * @covers ::addArgument
     * @covers ::getArguments
     */
    public function testAddAndGetArguments() : void
    {
        $argument = new Argument('firstArgument');
        $this->fixture->addArgument($argument);

        $this->assertEquals([$argument], $this->fixture->getArguments());
    }

    /**
     * @covers ::__construct
     * @covers ::getFqsen
     */
    public function testGetFqsen() : void
    {
        $this->assertSame($this->fqsen, $this->fixture->getFqsen());
    }

    /**
     * @covers ::__construct
     * @covers ::getDocBlock
     */
    public function testGetDocblock() : void
    {
        $this->assertSame($this->docBlock, $this->fixture->getDocBlock());
    }

    /**
     * @covers ::getReturnType
     * @covers ::__construct
     */
    public function testGetDefaultReturnType() : void
    {
        $method = new Function_($this->fqsen);
        $this->assertEquals(new Mixed_(), $method->getReturnType());
    }

    /**
     * @covers ::getReturnType
     * @covers ::__construct
     */
    public function testGetReturnTypeFromConstructor() : void
    {
        $returnType = new String_();
        $method     = new Function_(
            $this->fqsen,
            null,
            null,
            $returnType
        );

        $this->assertSame($returnType, $method->getReturnType());
    }
}
