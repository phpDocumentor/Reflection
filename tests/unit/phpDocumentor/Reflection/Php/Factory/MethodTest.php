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

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Argument as ArgumentDescriptor;
use phpDocumentor\Reflection\DocBlock as DocBlockDescriptor;
use phpDocumentor\Reflection\Php\Method as MethodDescriptor;
use phpDocumentor\Reflection\Php\Factory;
use Mockery as m;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\ClassMethod;

/**
 * Test case for \phpDocumentor\Reflection\Php\Factory\Method
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\Method
 * @covers ::<!public>
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
        $classMethodMock->shouldReceive('isPrivate')->once()->andReturn(false);
        $classMethodMock->shouldReceive('isProtected')->once()->andReturn(false);
        $classMethodMock->shouldReceive('getDocComment')->once()->andReturnNull();

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching')->never();

        /** @var MethodDescriptor $method */
        $method = $this->fixture->create($classMethodMock, $containerMock);

        $this->assertEquals('\SomeSpace\Class::function()', (string)$method->getFqsen());
        $this->assertEquals('public', (string)$method->getVisibility());
    }

    public function testCreateProtectedMethod()
    {
        $classMethodMock = $this->buildClassMethodMock();
        $classMethodMock->params = [];
        $classMethodMock->shouldReceive('isPrivate')->once()->andReturn(false);
        $classMethodMock->shouldReceive('isProtected')->once()->andReturn(true);
        $classMethodMock->shouldReceive('getDocComment')->once()->andReturnNull();

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching')->never();

        /** @var MethodDescriptor $method */
        $method = $this->fixture->create($classMethodMock, $containerMock);

        $this->assertEquals('\SomeSpace\Class::function()', (string)$method->getFqsen());
        $this->assertEquals('protected', (string)$method->getVisibility());
    }

    /**
     * @covers ::create
     */
    public function testCreateWithParameters()
    {
        $classMethodMock = $this->buildClassMethodMock();
        $classMethodMock->params = array('param1');
        $classMethodMock->shouldReceive('isPrivate')->once()->andReturn(true);
        $classMethodMock->shouldReceive('getDocComment')->once()->andReturnNull();

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching->create')
            ->once()
            ->with('param1', $containerMock, null)
            ->andReturn(new ArgumentDescriptor('param1'));

        /** @var MethodDescriptor $method */
        $method = $this->fixture->create($classMethodMock, $containerMock);

        $this->assertEquals('\SomeSpace\Class::function()', (string)$method->getFqsen());
        $this->assertTrue($method->isAbstract());
        $this->assertTrue($method->isFinal());
        $this->assertTrue($method->isStatic());
        $this->assertEquals('private', (string)$method->getVisibility());
    }

    /**
     * @covers ::create
     */
    public function testCreateWithDocBlock()
    {
        $doc = m::mock(Doc::class);
        $classMethodMock = $this->buildClassMethodMock();
        $classMethodMock->params = array();
        $classMethodMock->shouldReceive('isPrivate')->once()->andReturn(true);
        $classMethodMock->shouldReceive('getDocComment')->andReturn($doc);

        $docBlock = new DocBlockDescriptor('');

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching->create')
            ->once()
            ->with($doc, $containerMock, null)
            ->andReturn($docBlock);

        /** @var MethodDescriptor $method */
        $method = $this->fixture->create($classMethodMock, $containerMock);

        $this->assertEquals('\SomeSpace\Class::function()', (string)$method->getFqsen());
        $this->assertSame($docBlock, $method->getDocBlock());
    }


    private function buildClassMethodMock()
    {
        $methodMock = m::mock(ClassMethod::class);
        $methodMock->name = 'function';
        $methodMock->fqsen = new Fqsen('\SomeSpace\Class::function()');

        $methodMock->shouldReceive('isStatic')->once()->andReturn(true);
        $methodMock->shouldReceive('isFinal')->once()->andReturn(true);
        $methodMock->shouldReceive('isAbstract')->once()->andReturn(true);

        return $methodMock;
    }
}
