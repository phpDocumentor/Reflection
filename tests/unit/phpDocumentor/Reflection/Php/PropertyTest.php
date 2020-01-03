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
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the Property class.
 *
 * @coversDefaultClass phpDocumentor\Reflection\Php\Property
 */
class PropertyTest extends TestCase
{
    /** @var Fqsen */
    private $fqsen;

    /** @var Visibility */
    private $visibility;

    /** @var DocBlock */
    private $docBlock;

    protected function setUp() : void
    {
        $this->fqsen      = new Fqsen('\My\Class::$property');
        $this->visibility = new Visibility('private');
        $this->docBlock   = new DocBlock('');
    }

    protected function tearDown() : void
    {
        m::close();
    }

    /**
     * @covers ::getFqsen
     * @covers ::getName
     * @covers ::__construct
     */
    public function testGetFqsenAndGetName() : void
    {
        $property = new Property($this->fqsen);

        $this->assertSame($this->fqsen, $property->getFqsen());
        $this->assertEquals($this->fqsen->getName(), $property->getName());
    }

    /**
     * @covers ::isStatic
     * @covers ::__construct
     */
    public function testGettingWhetherPropertyIsStatic() : void
    {
        $property = new Property($this->fqsen, $this->visibility, $this->docBlock, null, false);
        $this->assertFalse($property->isStatic());

        $property = new Property($this->fqsen, $this->visibility, $this->docBlock, null, true);
        $this->assertTrue($property->isStatic());
    }

    /**
     * @covers ::getVisibility
     * @covers ::__construct
     */
    public function testGettingVisibility() : void
    {
        $property = new Property($this->fqsen, $this->visibility, $this->docBlock, null, true);

        $this->assertSame($this->visibility, $property->getVisibility());
    }

    /**
     * @covers ::getTypes
     * @covers ::addType
     */
    public function testSetAndGetTypes() : void
    {
        $property = new Property($this->fqsen, $this->visibility, $this->docBlock, null, true);
        $this->assertEquals([], $property->getTypes());

        $property->addType('a');
        $this->assertEquals(['a'], $property->getTypes());
    }

    /**
     * @covers ::getDefault
     * @covers ::__construct
     */
    public function testGetDefault() : void
    {
        $property = new Property($this->fqsen, $this->visibility, $this->docBlock, null, false);
        $this->assertNull($property->getDefault());

        $property = new Property($this->fqsen, $this->visibility, $this->docBlock, 'a', true);
        $this->assertEquals('a', $property->getDefault());
    }

    /**
     * @covers ::getDocBlock
     * @covers ::__construct
     */
    public function testGetDocBlock() : void
    {
        $property = new Property($this->fqsen, $this->visibility, $this->docBlock, null, false);
        $this->assertSame($this->docBlock, $property->getDocBlock());
    }
}
