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
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\Php\Function_ as FunctionDescriptor;
use phpDocumentor\Reflection\DocBlock as DocBlockDescriptor;
use phpDocumentor\Reflection\Php\Factory;
use phpDocumentor\Reflection\Php\Factory\Function_;
use Mockery as m;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Comment\Doc;

/**
 * Test case for \phpDocumentor\Reflection\Php\Factory\Function_
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\Function_
 * @covers ::<private>
 */
class Function_Test extends TestCase
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

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching')->never();

        /** @var FunctionDescriptor $function */
        $function = $this->fixture->create($functionMock, $containerMock);

        $this->assertEquals('\SomeSpace::function()', (string)$function->getFqsen());
    }

    /**
     * @covers ::create
     */
    public function testCreateWithParameters()
    {
        $functionMock = m::mock(\PhpParser\Node\Stmt\Function_::class);
        $functionMock->fqsen = new Fqsen('\SomeSpace::function()');
        $functionMock->params = array('param1');
        $functionMock->shouldReceive('getDocComment')->andReturnNull();

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching->create')
            ->once()
            ->with('param1', $containerMock)
            ->andReturn(new Argument('param1'));

        /** @var FunctionDescriptor $function */
        $function = $this->fixture->create($functionMock, $containerMock);

        $this->assertEquals('\SomeSpace::function()', (string)$function->getFqsen());
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

        $docBlock = new DocBlockDescriptor('');

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching->create')
            ->once()
            ->with($doc, $containerMock)
            ->andReturn($docBlock);

        /** @var FunctionDescriptor $function */
        $function = $this->fixture->create($functionMock, $containerMock);

        $this->assertEquals('\SomeSpace::function()', (string)$function->getFqsen());
        $this->assertSame($docBlock, $function->getDocBlock());
    }

}
