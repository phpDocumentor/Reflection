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

use phpDocumentor\Descriptor\Argument;
use phpDocumentor\Descriptor\Method as MethodDescriptor;
use phpDocumentor\Reflection\Php\Factory;
use Mockery as m;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Node\Stmt\ClassMethod;

/**
 * Test case for \phpDocumentor\Reflection\Php\Factory\Method
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\Method
 * @covers ::<private>
 */
class MethodTest extends TestCase
{
    protected function setUp()
    {
        $this->fixture = new Method();
    }

    /**
     * @covers ::matches
     */
    public function testMatches()
    {
        $this->assertFalse($this->fixture->matches(new \stdClass()));
        $this->assertTrue($this->fixture->matches(m::mock(ClassMethod::class)));
    }

    /**
     * @covers ::create
     */
    public function testCreateWithoutParameters()
    {
        $classMethodMock = $this->buildClassMethodMock();
        $classMethodMock->params = [];
        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching')->never();

        /** @var MethodDescriptor $method */
        $method = $this->fixture->create($classMethodMock, $containerMock);

        $this->assertEquals('\SomeSpace\Class::function()', (string)$method->getFqsen());
    }

    /**
     * @covers ::create
     */
    public function testCreateWithParameters()
    {
        $classMethodMock = $this->buildClassMethodMock();
        $classMethodMock->params = array('param1');

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching->create')
            ->once()
            ->with('param1', $containerMock)
            ->andReturn(new Argument('param1'));

        /** @var MethodDescriptor $method */
        $method = $this->fixture->create($classMethodMock, $containerMock);

        $this->assertEquals('\SomeSpace\Class::function()', (string)$method->getFqsen());
    }

    private function buildClassMethodMock()
    {
        $functionMock = m::mock(ClassMethod::class);
        $functionMock->name = '\SomeSpace\Class::function';

        $functionMock->shouldReceive('isPrivate')->once()->andReturn(true);
        $functionMock->shouldReceive('isStatic')->once()->andReturn(true);
        $functionMock->shouldReceive('isFinal')->once()->andReturn(true);
        $functionMock->shouldReceive('isAbstract')->once()->andReturn(true);

        return $functionMock;
    }
}
