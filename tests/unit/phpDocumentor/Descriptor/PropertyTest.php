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
use phpDocumentor\Descriptor\Tag\AuthorDescriptor;
use phpDocumentor\Descriptor\Tag\VarDescriptor;
use phpDocumentor\Descriptor\Tag\VersionDescriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Visibility;

/**
 * Tests the functionality for the Property class.
 *
 * @coversDefaultClass phpDocumentor\Descriptor\Property
 */
class PropertyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Fqsen
     */
    private $fqsen;

    /**
     * @var Visibility
     */
    private $visibility;

    protected function setUp()
    {
        $this->fqsen = new Fqsen('\My\Class::$property');
        $this->visibility = new Visibility('private');
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
        $property = new Property($this->fqsen, $this->visibility, null, false);
        $this->assertFalse($property->isStatic());

        $property = new Property($this->fqsen, $this->visibility, null, true);
        $this->assertTrue($property->isStatic());
    }

    /**
     * @covers ::getVisibility
     * @covers ::__construct
     */
    public function testGettingVisibility()
    {
        $property = new Property($this->fqsen, $this->visibility, null, true);

        $this->assertSame($this->visibility, $property->getVisibility());
    }

    /**
     * @covers ::getTypes
     */
    public function testSetAndGetTypes()
    {
        $property = new Property($this->fqsen, $this->visibility, null, true);
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
        $property = new Property($this->fqsen, $this->visibility, null, false);
        $this->assertNull($property->getDefault());

        $property = new Property($this->fqsen, $this->visibility, 'a', true);
        $this->assertEquals('a', $property->getDefault());
    }
}
