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
 * @uses \phpDocumentor\Reflection\Php\Visibility
 *
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Method
 * @covers ::__construct
 * @covers ::<private>
 */
final class MethodTest extends TestCase
{
    /** @var Fqsen */
    private $fqsen;

    /** @var Visibility */
    private $visibility;

    /** @var DocBlock */
    private $docblock;

    protected function setUp() : void
    {
        $this->fqsen = new Fqsen('\My\Space::MyMethod()');
        $this->visibility = new Visibility('private');
        $this->docblock = new DocBlock('');
    }

    /**
     * @covers ::getFqsen
     * @covers ::getName
     */
    public function testGetFqsenAndGetName() : void
    {
        $method = new Method($this->fqsen);

        $this->assertSame($this->fqsen, $method->getFqsen());
        $this->assertEquals($this->fqsen->getName(), $method->getName());
    }

    /**
     * @covers ::getDocblock
     */
    public function testGetDocBlock() : void
    {
        $method = new Method($this->fqsen, $this->visibility, $this->docblock);

        $this->assertSame($this->docblock, $method->getDocBlock());
    }

    /**
     * @uses \phpDocumentor\Reflection\Php\Argument
     *
     * @covers ::getArguments
     * @covers ::addArgument
     */
    public function testAddingAndGettingArguments() : void
    {
        $method = new Method($this->fqsen);
        $this->assertEquals([], $method->getArguments());

        $argument = new Argument('myArgument');
        $method->addArgument($argument);

        $this->assertEquals([$argument], $method->getArguments());
    }

    /**
     * @covers ::isAbstract
     */
    public function testGettingWhetherMethodIsAbstract() : void
    {
        $method = new Method($this->fqsen, $this->visibility, $this->docblock, false);
        $this->assertFalse($method->isAbstract());

        $method = new Method($this->fqsen, $this->visibility, $this->docblock, true);
        $this->assertTrue($method->isAbstract());
    }

    /**
     * @covers ::isFinal
     */
    public function testGettingWhetherMethodIsFinal() : void
    {
        $method = new Method($this->fqsen, $this->visibility, $this->docblock, false, false, false);
        $this->assertFalse($method->isFinal());

        $method = new Method($this->fqsen, $this->visibility, $this->docblock, false, false, true);
        $this->assertTrue($method->isFinal());
    }

    /**
     * @covers ::isStatic
     */
    public function testGettingWhetherMethodIsStatic() : void
    {
        $method = new Method($this->fqsen, $this->visibility, $this->docblock, false, false, false);
        $this->assertFalse($method->isStatic());

        $method = new Method($this->fqsen, $this->visibility, $this->docblock, false, true, false);
        $this->assertTrue($method->isStatic());
    }

    /**
     * @covers ::getVisibility
     */
    public function testGettingVisibility() : void
    {
        $method = new Method($this->fqsen, $this->visibility, $this->docblock, false, false, false);
        $this->assertSame($this->visibility, $method->getVisibility());
    }

    /**
     * @covers ::getVisibility
     */
    public function testGetDefaultVisibility() : void
    {
        $method = new Method($this->fqsen);
        $this->assertEquals(new Visibility('public'), $method->getVisibility());
    }

    /**
     * @covers ::getReturnType
     */
    public function testGetDefaultReturnType() : void
    {
        $method = new Method($this->fqsen);
        $this->assertEquals(new Mixed_(), $method->getReturnType());
    }

    /**
     * @covers ::getReturnType
     */
    public function testGetReturnTypeFromConstructor() : void
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

    /**
     * @covers ::getLocation
     */
    public function testLineNumberIsMinusOneWhenNoneIsProvided() : void
    {
        $fixture = new Method($this->fqsen);

        $this->assertSame(-1, $fixture->getLocation()->getLineNumber());
        $this->assertSame(0, $fixture->getLocation()->getColumnNumber());
    }

    /**
     * @uses \phpDocumentor\Reflection\Location
     *
     * @covers ::getLocation
     */
    public function testLineAndColumnNumberIsReturnedWhenALocationIsProvided() : void
    {
        $fixture = new Method($this->fqsen, null, null, false, false, false, new Location(100, 20));

        $this->assertSame(100, $fixture->getLocation()->getLineNumber());
        $this->assertSame(20, $fixture->getLocation()->getColumnNumber());
    }
}
