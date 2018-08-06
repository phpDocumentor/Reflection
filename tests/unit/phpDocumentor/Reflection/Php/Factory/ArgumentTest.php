<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Factory;

use Mockery as m;
use phpDocumentor\Reflection\Php\Argument as ArgumentDescriptor;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use phpDocumentor\Reflection\PrettyPrinter;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use stdClass;

/**
 * Class ArgumentTest
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\Argument
 * @covers ::__construct
 * @covers ::<!public>
 */
class ArgumentTest extends TestCase
{
    protected function setUp()
    {
        $this->fixture = new Argument(new PrettyPrinter());
    }

    protected function tearDown()
    {
        m::close();
    }

    /**
     * @covers ::matches
     */
    public function testMatches()
    {
        $this->assertFalse($this->fixture->matches(new stdClass()));
        $this->assertTrue($this->fixture->matches(m::mock(Param::class)));
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        $factory = new ProjectFactoryStrategies([]);

        $argMock = m::mock(Param::class);
        $argMock->var = new stdClass;
        $argMock->var->name = 'myArgument';
        $argMock->default = new String_('MyDefault');
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
