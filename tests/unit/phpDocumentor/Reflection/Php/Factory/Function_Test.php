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
use phpDocumentor\Reflection\DocBlock as DocBlockDescriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\Php\Function_ as FunctionDescriptor;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Nullable;
use PhpParser\Comment\Doc;
use PhpParser\Node\NullableType;

/**
 * Test case for \phpDocumentor\Reflection\Php\Factory\Function_
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\Function_
 * @covers ::<!public>
 */
// @codingStandardsIgnoreStart
class Function_Test extends TestCase
// @codingStandardsIgnoreEnd
{
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
        $functionMock->fqsen = new Fqsen('\SomeSpace::function()');
        $functionMock->params = [];
        $functionMock->shouldReceive('getDocComment')->andReturnNull();
        $functionMock->shouldReceive('getLine')->andReturn(1);
        $functionMock->shouldReceive('getReturnType')->andReturnNull();

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching')->never();

        /** @var FunctionDescriptor $function */
        $function = $this->fixture->create($functionMock, $containerMock);

        $this->assertEquals('\SomeSpace::function()', (string) $function->getFqsen());
    }

    /**
     * @covers ::create
     */
    public function testCreateWithParameters()
    {
        $functionMock = m::mock(\PhpParser\Node\Stmt\Function_::class);
        $functionMock->fqsen = new Fqsen('\SomeSpace::function()');
        $functionMock->params = ['param1'];
        $functionMock->shouldReceive('getDocComment')->andReturnNull();
        $functionMock->shouldReceive('getLine')->andReturn(1);
        $functionMock->shouldReceive('getReturnType')->andReturnNull();

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching->create')
            ->once()
            ->with('param1', $containerMock, null)
            ->andReturn(new Argument('param1'));

        /** @var FunctionDescriptor $function */
        $function = $this->fixture->create($functionMock, $containerMock);

        $this->assertEquals('\SomeSpace::function()', (string) $function->getFqsen());
    }

    /**
     * @covers ::create
     */
    public function testReturnTypeResolving()
    {
        $functionMock = m::mock(\PhpParser\Node\Stmt\Function_::class);
        $functionMock->fqsen = new Fqsen('\SomeSpace::function()');
        $functionMock->params = [];
        $functionMock->shouldReceive('getDocComment')->andReturnNull();
        $functionMock->shouldReceive('getLine')->andReturn(1);
        $functionMock->shouldReceive('getReturnType')->times(3)->andReturn('int');

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching')->never();

        /** @var FunctionDescriptor $function */
        $function = $this->fixture->create($functionMock, $containerMock);

        $this->assertEquals(new Integer(), $function->getReturnType());
    }

    /**
     * @covers ::create
     */
    public function testReturnTypeNullableResolving()
    {
        $functionMock = m::mock(\PhpParser\Node\Stmt\Function_::class);
        $functionMock->fqsen = new Fqsen('\SomeSpace::function()');
        $functionMock->params = [];
        $functionMock->shouldReceive('getDocComment')->andReturnNull();
        $functionMock->shouldReceive('getLine')->andReturn(1);
        $functionMock->shouldReceive('getReturnType')->times(3)->andReturn(new NullableType('int'));

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching')->never();

        /** @var FunctionDescriptor $method */
        $function = $this->fixture->create($functionMock, $containerMock);

        $this->assertEquals(new Nullable(new Integer()), $function->getReturnType());
    }

    /**
     * @covers ::create
     */
    public function testCreateWithDocBlock()
    {
        $doc = m::mock(Doc::class);
        $functionMock = m::mock(\PhpParser\Node\Stmt\Function_::class);
        $functionMock->fqsen = new Fqsen('\SomeSpace::function()');
        $functionMock->params = [];
        $functionMock->shouldReceive('getDocComment')->andReturn($doc);
        $functionMock->shouldReceive('getLine')->andReturn(1);
        $functionMock->shouldReceive('getReturnType')->andReturnNull();

        $docBlock = new DocBlockDescriptor('');

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching->create')
            ->once()
            ->with($doc, $containerMock, null)
            ->andReturn($docBlock);

        /** @var FunctionDescriptor $function */
        $function = $this->fixture->create($functionMock, $containerMock);

        $this->assertEquals('\SomeSpace::function()', (string) $function->getFqsen());
        $this->assertSame($docBlock, $function->getDocBlock());
    }
}
