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
        $factory = new ProjectFactoryStrategies(array());

        $argMock = m::mock(Param::class);
        $argMock->name = 'myArgument';
        $argMock->default = new String_('MyDefault');
        $argMock->byRef = true;
        $argMock->variadic = true;

        $argument = $this->fixture->create($argMock, $factory);

        $this->assertInstanceOf(ArgumentDescriptor::class, $argument);
        $this->assertEquals('myArgument', $argument->getName());
        $this->assertTrue($argument->isByReference());
        $this->assertTrue($argument->isVariadic());
        $this->assertEquals('MyDefault', $argument->getDefault());
        $this->assertSame([], $argument->getTypes());
    }

    /**
     * @covers ::create
     * @dataProvider dataArgumentTypes
     * @param string $type An argument type.
     */
    public function testCreateWithTypes( $type )
    {
        $factory = new ProjectFactoryStrategies(array());

        $argMock = m::mock(Param::class);
        $argMock->name = 'myArgument';
        $argMock->type = $type;

        $argument = $this->fixture->create($argMock, $factory);

        $this->assertInstanceOf(ArgumentDescriptor::class, $argument);
        $this->assertSame([$type], $argument->getTypes());
    }

    /**
     * Data provider for possible argument types
     */
    public function dataArgumentTypes()
    {
        return [
            [
                'array',
            ],
            [
                'callable',
            ],
            [
                'bool',
            ],
            [
                'float',
            ],
            [
                'int',
            ],
            [
                'string',
            ],
            [
                '\my\classname',
            ],
        ];
    }

}
