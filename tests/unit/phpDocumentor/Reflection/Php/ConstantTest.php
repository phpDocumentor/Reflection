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
 * @uses \phpDocumentor\Reflection\DocBlock
 * @uses \phpDocumentor\Reflection\Php\Visibility
 * @uses \phpDocumentor\Reflection\Fqsen
 *
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Constant
 * @covers ::__construct
 * @covers ::<private>
 */
final class ConstantTest extends TestCase
{
    /** @var Constant $fixture */
    protected $fixture;

    /** @var Fqsen */
    private $fqsen;

    /** @var DocBlock */
    private $docBlock;

    /** @var string */
    private $value = 'Value';

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp() : void
    {
        $this->fqsen = new Fqsen('\MySpace\CONSTANT');
        $this->docBlock = new DocBlock('');
        $this->fixture = new Constant($this->fqsen, $this->docBlock, $this->value);
    }

    /**
     * @covers ::getValue
     * @covers ::__construct
     */
    public function testGetValue() : void
    {
        $this->assertSame($this->value, $this->fixture->getValue());
    }

    /**
     * @covers ::getFqsen
     * @covers ::getName
     */
    public function testGetFqsen() : void
    {
        $this->assertSame($this->fqsen, $this->fixture->getFqsen());
        $this->assertSame($this->fqsen->getName(), $this->fixture->getName());
    }

    /**
     * @covers ::getDocBlock
     */
    public function testGetDocblock() : void
    {
        $this->assertSame($this->docBlock, $this->fixture->getDocBlock());
    }

    /**
     * @covers ::getVisibility
     */
    public function testGetVisibility() : void
    {
        $this->assertEquals(new Visibility(Visibility::PUBLIC_), $this->fixture->getVisibility());
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
        $fixture = new Constant($this->fqsen, $this->docBlock, null, new Location(100, 20));

        $this->assertSame(100, $fixture->getLocation()->getLineNumber());
        $this->assertSame(20, $fixture->getLocation()->getColumnNumber());
    }
}
