<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php;

use \Mockery as m;
use phpDocumentor\Reflection\Fqsen;

/**
 * Tests the functionality for the Trait_ class.
 * @coversDefaultClass phpDocumentor\Reflection\Php\Trait_
 */
class Trait_Test extends \PHPUnit_Framework_TestCase
{
    /** @var Trait_ $fixture */
    protected $fixture;

    /**
     * @var Fqsen
     */
    private $fqsen;
    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp()
    {
        $this->fqsen = new Fqsen('\MyTrait');
        $this->fixture = new Trait_($this->fqsen);
    }

    /**
     * @covers ::getFqsen
     * @covers ::getName
     * @covers ::__construct
     */
    public function testGetFqsenAndGetName()
    {
        $this->assertSame($this->fqsen, $this->fixture->getFqsen());
        $this->assertEquals($this->fqsen->getName(), $this->fixture->getName());
    }

    /**
     * @covers ::addProperty
     * @covers ::getProperties
     */
    public function testAddAndGettingProperties()
    {
        $this->assertEquals(array(), $this->fixture->getProperties());

        $property = new Property(new Fqsen('\MyTrait::$myProperty'));

        $this->fixture->addProperty($property);

        $this->assertEquals(array('\MyTrait::$myProperty' => $property), $this->fixture->getProperties());
    }

    /**
     * @covers ::addMethod
     * @covers ::getMethods
     */
    public function testAddAndGettingMethods()
    {
        $this->assertEquals(array(), $this->fixture->getMethods());

        $method = new Method(new Fqsen('\MyTrait::myMethod()'));

        $this->fixture->addMethod($method);

        $this->assertEquals(array('\MyTrait::myMethod()' => $method), $this->fixture->getMethods());
    }
}
