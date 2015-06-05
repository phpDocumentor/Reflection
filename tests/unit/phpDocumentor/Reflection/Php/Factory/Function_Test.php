<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2015 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Factory;

use phpDocumentor\Descriptor\Function_ as FunctionDescriptor;
use phpDocumentor\Reflection\Php\Factory;
use phpDocumentor\Reflection\Php\Factory\Function_;
use Mockery as m;

/**
 * Test case for \phpDocumentor\Reflection\Php\Factory\Function_
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\Function_
 */
class Function_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Function_
     */
    private $fixture;

    protected function setUp()
    {
        $this->fixture = new Function_();
    }

    /**
     * @covers ::matches
     */
    public function testMatches()
    {
        $this->assertFalse($this->fixture->matches(new \stdClass()));
        $this->assertTrue($this->fixture->matches(m::mock(\PhpParser\Node\Stmt\Function_::class)));
    }

    /**
     * @covers ::create
     */
    public function testCreateWithoutParameters()
    {
        $functionMock = m::mock(\PhpParser\Node\Stmt\Function_::class);
        $factoryMock = m::mock(ProjectFactory::class);
        $factoryMock->shouldReceive('create')->never();

        $this->fixture->create($functionMock, $factoryMock);
    }
}
