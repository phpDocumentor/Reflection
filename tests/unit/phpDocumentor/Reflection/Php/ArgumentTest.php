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

use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the Argument class.
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Argument
 */
class ArgumentTest extends TestCase
{
    /**
     * @covers ::getType
     */
    public function testGetTypes()
    {
        $argument = new Argument('myArgument', null, 'myDefaultValue', true, true);
        $this->assertInstanceOf(Mixed_::class, $argument->getType());

        $argument = new Argument(
            'myArgument',
            new String_(),
            'myDefaultValue',
            true,
            true
        );
        $this->assertEquals(new String_(), $argument->getType());
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
        $argument = new Argument('myArgument', null, 'myDefaultValue', true, true);
        $this->assertEquals('myDefaultValue', $argument->getDefault());

        $argument = new Argument('myArgument', null, null, true, true);
        $this->assertNull($argument->getDefault());
    }

    /**
     * @covers ::__construct
     * @covers ::isByReference
     */
    public function testGetWhetherArgumentIsPassedByReference()
    {
        $argument = new Argument('myArgument', null, 'myDefaultValue', true, true);
        $this->assertTrue($argument->isByReference());

        $argument = new Argument('myArgument', null, null, false, true);
        $this->assertFalse($argument->isByReference());
    }

    /**
     * @covers ::__construct
     * @covers ::isVariadic
     */
    public function testGetWhetherArgumentisVariadic()
    {
        $argument = new Argument('myArgument', null, 'myDefaultValue', true, true);
        $this->assertTrue($argument->isVariadic());

        $argument = new Argument('myArgument', null, 'myDefaultValue', true, false);
        $this->assertFalse($argument->isVariadic());
    }
}
