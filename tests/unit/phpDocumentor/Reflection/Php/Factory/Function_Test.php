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
use phpDocumentor\Reflection\DocBlock as DocBlockDescriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\Php\Function_ as FunctionDescriptor;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Comment\Doc;
use stdClass;

/**
 * @uses   \phpDocumentor\Reflection\Php\Factory\Function_::matches
 * @uses   \phpDocumentor\Reflection\Php\Function_
 * @uses   \phpDocumentor\Reflection\Php\Argument
 * @uses   \phpDocumentor\Reflection\Php\Factory\Type
 *
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\Function_
 * @covers \phpDocumentor\Reflection\Php\Factory\AbstractFactory
 * @covers ::<private>
 * @covers ::<protected>
 */
final class Function_Test extends TestCase
{
    protected function setUp() : void
    {
        $this->fixture = new Function_();
    }

    /**
     * @covers ::matches
     */
    public function testMatches() : void
    {
        $this->assertFalse($this->fixture->matches(new stdClass()));
        $this->assertTrue($this->fixture->matches(m::mock(\PhpParser\Node\Stmt\Function_::class)));
    }

    /**
     * @covers ::create
     */
    public function testCreateWithoutParameters() : void
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
    public function testCreateWithParameters() : void
    {
        $functionMock = m::mock(\PhpParser\Node\Stmt\Function_::class);
        $functionMock->fqsen = new Fqsen('\SomeSpace::function()');
        $functionMock->params = ['param1'];
        $functionMock->shouldReceive('getDocComment')->andReturnNull();
        $functionMock->shouldReceive('getLine')->andReturn(1);
        $functionMock->shouldReceive('getReturnType')->andReturnNull();

        $strategyMock = m::mock(ProjectFactoryStrategy::class);
        $containerMock = m::mock(StrategyContainer::class);

        $strategyMock->shouldReceive('create')
            ->with('param1', $containerMock, null)
            ->andReturn(new Argument('param1'));

        $containerMock->shouldReceive('findMatching')
            ->with('param1')
            ->andReturn($strategyMock);

        /** @var FunctionDescriptor $function */
        $function = $this->fixture->create($functionMock, $containerMock);

        $this->assertEquals('\SomeSpace::function()', (string) $function->getFqsen());
    }

    /**
     * @covers ::create
     */
    public function testCreateWithDocBlock() : void
    {
        $doc = m::mock(Doc::class);
        $functionMock = m::mock(\PhpParser\Node\Stmt\Function_::class);
        $functionMock->fqsen = new Fqsen('\SomeSpace::function()');
        $functionMock->params = [];
        $functionMock->shouldReceive('getDocComment')->andReturn($doc);
        $functionMock->shouldReceive('getLine')->andReturn(1);
        $functionMock->shouldReceive('getReturnType')->andReturnNull();

        $docBlock = new DocBlockDescriptor('');
        $strategyMock = m::mock(ProjectFactoryStrategy::class);
        $containerMock = m::mock(StrategyContainer::class);

        $strategyMock->shouldReceive('create')
            ->with($doc, $containerMock, null)
            ->andReturn($docBlock);

        $containerMock->shouldReceive('findMatching')
            ->with($doc)
            ->andReturn($strategyMock);

        /** @var FunctionDescriptor $function */
        $function = $this->fixture->create($functionMock, $containerMock);

        $this->assertEquals('\SomeSpace::function()', (string) $function->getFqsen());
        $this->assertSame($docBlock, $function->getDocBlock());
    }
}
