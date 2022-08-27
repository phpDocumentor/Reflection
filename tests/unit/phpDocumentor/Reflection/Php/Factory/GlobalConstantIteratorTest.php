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
use phpDocumentor\Reflection\Fqsen;
use PhpParser\Comment\Doc;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Const_ as ConstStatement;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\GlobalConstantIterator
 * @covers ::__construct
 * @covers ::<private>
 */
final class GlobalConstantIteratorTest extends m\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @covers ::current()
     * @covers ::next()
     * @covers ::valid()
     * @covers ::rewind()
     * @covers ::getName()
     * @covers ::getFqsen()
     */
    public function testIterateProps(): void
    {
        $const1 = new Const_('\Space\MY_CONST1', new Variable('a'));
        $const1->setAttribute('fqsen', new Fqsen((string) $const1->name));
        $const2 = new Const_('\Space\MY_CONST2', new Variable('b'));
        $const2->setAttribute('fqsen', new Fqsen((string) $const2->name));

        $globalConstantNode = new ConstStatement([$const1, $const2]);

        $i = 1;
        foreach (new GlobalConstantIterator($globalConstantNode) as $constant) {
            $this->assertEquals('\Space\MY_CONST' . $i, $constant->getName());
            $this->assertEquals('\Space\MY_CONST' . $i, (string) $constant->getFqsen());

            ++$i;
        }
    }

    /**
     * @covers ::key()
     * @covers ::next()
     */
    public function testKey(): void
    {
        $constant = m::mock(ConstStatement::class);

        $fixture = new GlobalConstantIterator($constant);

        $this->assertEquals(0, $fixture->key());
        $fixture->next();
        $this->assertEquals(1, $fixture->key());
    }

    /**
     * @covers ::__construct
     * @covers ::getLine
     */
    public function testProxyMethods(): void
    {
        $constant = m::mock(ConstStatement::class);
        $constant->shouldReceive('getLine')->once()->andReturn(10);

        $fixture = new GlobalConstantIterator($constant);

        $this->assertEquals(10, $fixture->getLine());
    }

    /**
     * @covers ::getDocComment
     */
    public function testGetDocCommentPropFirst(): void
    {
        $const = m::mock(Const_::class);
        $constants = m::mock(ConstStatement::class);
        $constants->consts = [$const];

        $const->shouldReceive('getDocComment')->once()->andReturn(new Doc('test'));
        $constants->shouldReceive('getDocComment')->never();

        $fixture = new GlobalConstantIterator($constants);

        $this->assertEquals('test', $fixture->getDocComment()->getText());
    }

    /**
     * @covers ::getDocComment
     */
    public function testGetDocComment(): void
    {
        $const = m::mock(Const_::class);
        $constants = m::mock(ConstStatement::class);
        $constants->consts = [$const];

        $const->shouldReceive('getDocComment')->once()->andReturnNull();
        $constants->shouldReceive('getDocComment')->once()->andReturn(new Doc('test'));

        $fixture = new GlobalConstantIterator($constants);

        $this->assertEquals('test', $fixture->getDocComment()->getText());
    }
}
