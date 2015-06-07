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

use phpDocumentor\Descriptor\Argument as ArgumentDescriptor;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use PhpParser\Node\Param;
use Mockery as m;

/**
 * Class ArgumentTest
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\Argument
 */
class ArgumentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Argument
     */
    private $fixture;

    protected function setUp()
    {
        $this->fixture = new Argument();
    }

    /**
     * @covers ::matches
     */
    public function testMatches()
    {
        $this->assertFalse($this->fixture->matches(new \stdClass()));
        $this->assertTrue($this->fixture->matches(m::mock(Param::class)));
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        $factory = new ProjectFactoryStrategies(array());

        $argMock = m::mock(Param::class);
        $argMock->name = 'myArgument';
        $argMock->default = 'MyDefault';
        $argMock->byRef = true;
        $argMock->variadic = true;

        $argument = $this->fixture->create($argMock, $factory);

        $this->assertInstanceOf(ArgumentDescriptor::class, $argument);
        $this->assertEquals('myArgument', $argument->getName());
        $this->assertTrue($argument->isByReference());
        $this->assertTrue($argument->isVariadic());
        $this->assertEquals('MyDefault', $argument->getDefault());
    }

}
