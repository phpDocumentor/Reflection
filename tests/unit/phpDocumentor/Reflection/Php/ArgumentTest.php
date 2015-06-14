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

namespace phpDocumentor\Reflection\Php;

/**
 * Tests the functionality for the Argument class.
 * @coversDefaultClass phpDocumentor\Reflection\Php\Argument
 */
class ArgumentTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::getTypes
     * @covers ::addType
     */
    public function testGetTypes()
    {
        $argument = new Argument('myArgument', 'myDefaultValue', true, true);
        $this->assertSame(array(), $argument->getTypes());

        $argument->addType(1);

        $this->assertSame(array(1), $argument->getTypes());
    }

    /**
     * @covers ::__construct
     * @covers ::getName
     */
    public function testGetName()
    {
        $argument = new Argument('myArgument', null, true, true);
        $this->assertEquals('myArgument', $argument->getName());
    }

    /**
     * @covers ::__construct
     * @covers ::getDefault
     */
    public function testGetDefault()
    {
        $argument = new Argument('myArgument', 'myDefaultValue', true, true);
        $this->assertEquals('myDefaultValue', $argument->getDefault());

        $argument = new Argument('myArgument', null, true, true);
        $this->assertNull($argument->getDefault());
    }

    /**
     * @covers ::__construct
     * @covers ::isByReference
     */
    public function testGetWhetherArgumentIsPassedByReference()
    {
        $argument = new Argument('myArgument', 'myDefaultValue', true, true);
        $this->assertTrue($argument->isByReference());

        $argument = new Argument('myArgument', null, false, true);
        $this->assertFalse($argument->isByReference());
    }

    /**
     * @covers ::__construct
     * @covers ::isVariadic
     */
    public function testGetWhetherArgumentisVariadic()
    {
        $argument = new Argument('myArgument', 'myDefaultValue', true, true);
        $this->assertTrue($argument->isVariadic());

        $argument = new Argument('myArgument', 'myDefaultValue', true, false);
        $this->assertFalse($argument->isVariadic());
    }
}
