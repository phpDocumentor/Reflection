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

use \Mockery as m;
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
    /**
     * @var Fqsen
     */
    private $fqsen;

    /**
     * @var Visibility
     */
    private $visibility;

    /**
     * @var DocBlock
     */
    private $docBlock;

    protected function setUp()
    {
        $this->fqsen = new Fqsen('\My\Class::$property');
        $this->visibility = new Visibility('private');
        $this->docBlock = new DocBlock('');
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
        $property = new Property($this->fqsen);

        $this->assertSame($this->fqsen, $property->getFqsen());
        $this->assertEquals($this->fqsen->getName(), $property->getName());
    }

    /**
     * @covers ::isStatic
     * @covers ::__construct
     */
    public function testGettingWhetherPropertyIsStatic()
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
    public function testGettingVisibility()
    {
        $property = new Property($this->fqsen, $this->visibility, $this->docBlock, null, true);

        $this->assertSame($this->visibility, $property->getVisibility());
    }

    /**
     * @covers ::getTypes
     * @covers ::addType
     */
    public function testSetAndGetTypes()
    {
        $property = new Property($this->fqsen, $this->visibility, $this->docBlock, null, true);
        $this->assertEquals(array(), $property->getTypes());

        $property->addType('a');
        $this->assertEquals(array('a'), $property->getTypes());
    }

    /**
     * @covers ::getDefault
     * @covers ::__construct
     */
    public function testGetDefault()
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
    public function testGetDocBlock()
    {
        $property = new Property($this->fqsen, $this->visibility, $this->docBlock, null, false);
        $this->assertSame($this->docBlock, $property->getDocBlock());
    }
}
