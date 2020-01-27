<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Factory;

use Mockery as m;
use phpDocumentor\Reflection\Php\Argument as ArgumentDescriptor;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use stdClass;

/**
 * @uses \phpDocumentor\Reflection\Php\Argument
 * @uses \phpDocumentor\Reflection\Php\Factory\Argument::matches
 * @uses \phpDocumentor\Reflection\Php\ProjectFactoryStrategies
 * @uses \phpDocumentor\Reflection\Php\Factory\Type
 *
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\Argument
 * @covers \phpDocumentor\Reflection\Php\Factory\AbstractFactory
 * @covers ::__construct
 * @covers ::<protected>
 * @covers ::<private>
 */
class ArgumentTest extends TestCase
{
    protected function setUp() : void
    {
        $this->fixture = new Argument(new PrettyPrinter());
    }

    /**
     * @covers ::matches
     */
    public function testMatches() : void
    {
        $this->assertFalse($this->fixture->matches(new stdClass()));
        $this->assertTrue($this->fixture->matches(m::mock(Param::class)));
    }

    /**
     * @covers ::create
     */
    public function testCreate() : void
    {
        $factory = new ProjectFactoryStrategies([]);

        $argMock = m::mock(Param::class);
        $argMock->var = new Variable('myArgument');
        $argMock->default = new String_('MyDefault');
        $argMock->byRef = true;
        $argMock->variadic = true;

        $argument = $this->fixture->create($argMock, $factory);

        $this->assertInstanceOf(ArgumentDescriptor::class, $argument);
        $this->assertEquals('myArgument', $argument->getName());
        $this->assertTrue($argument->isByReference());
        $this->assertTrue($argument->isVariadic());
        $this->assertEquals('\'MyDefault\'', $argument->getDefault());
    }
}
