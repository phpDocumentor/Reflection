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
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;

/**
 * @uses \phpDocumentor\Reflection\Php\Argument
 * @uses \phpDocumentor\Reflection\DocBlock
 * @uses \phpDocumentor\Reflection\Fqsen
 *
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Function_
 * @covers ::__construct
 * @covers ::<private>
 */
final class Function_Test extends TestCase
{
    /** @var Function_ $fixture */
    private $fixture;

    /** @var Fqsen */
    private $fqsen;

    /** @var DocBlock */
    private $docBlock;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp() : void
    {
        $this->fqsen = new Fqsen('\space\MyFunction()');
        $this->docBlock = new DocBlock('aa');
        $this->fixture = new Function_($this->fqsen, $this->docBlock);
    }

    /**
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
     * @covers ::getFqsen
     */
    public function testGetFqsen() : void
    {
        $this->assertSame($this->fqsen, $this->fixture->getFqsen());
    }

    /**
     * @covers ::getDocBlock
     */
    public function testGetDocblock() : void
    {
        $this->assertSame($this->docBlock, $this->fixture->getDocBlock());
    }

    /**
     * @covers ::getReturnType
     */
    public function testGetDefaultReturnType() : void
    {
        $method = new Function_($this->fqsen);
        $this->assertEquals(new Mixed_(), $method->getReturnType());
    }

    /**
     * @covers ::getReturnType
     */
    public function testGetReturnTypeFromConstructor() : void
    {
        $returnType = new String_();
        $method = new Function_(
            $this->fqsen,
            null,
            null,
            $returnType
        );

        $this->assertSame($returnType, $method->getReturnType());
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
        $fixture = new Function_($this->fqsen, $this->docBlock, new Location(100, 20));

        $this->assertSame(100, $fixture->getLocation()->getLineNumber());
        $this->assertSame(20, $fixture->getLocation()->getColumnNumber());
    }
}
