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
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Reflection\Fqsen;
use PhpParser\Comment\Doc;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassConst;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\ClassConstantIterator
 * @covers ::__construct
 * @covers ::<private>
 */
final class ClassConstantIteratorTest extends MockeryTestCase
{
    /**
     * @covers ::current()
     * @covers ::next()
     * @covers ::valid()
     * @covers ::rewind()
     * @covers ::getName()
     * @covers ::getValue()
     * @covers ::getFqsen()
     */
    public function testIterateProps(): void
    {
        $const1 = new Const_('\Space\MyClass::MY_CONST1', new Variable('1'));
        $const1->setAttribute('fqsen', new Fqsen((string) $const1->name));
        $const2 = new Const_('\Space\MyClass::MY_CONST2', new Variable('2'));
        $const2->setAttribute('fqsen', new Fqsen((string) $const2->name));

        $classConstantNode = new ClassConst([$const1, $const2]);

        $i = 1;
        foreach (new ClassConstantIterator($classConstantNode) as $constant) {
            $this->assertEquals('\Space\MyClass::MY_CONST' . $i, $constant->getName());
            $this->assertEquals('\Space\MyClass::MY_CONST' . $i, (string) $constant->getFqsen());
            $this->assertEquals($i, $constant->getValue()->name);
            ++$i;
        }
    }

    /**
     * @covers ::key()
     * @covers ::next()
     */
    public function testKey(): void
    {
        $constantMock = m::mock(ClassConst::class);

        $fixture = new ClassConstantIterator($constantMock);

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
        $constantMock = m::mock(ClassConst::class);
        $constantMock->shouldReceive('getLine')->once()->andReturn(10);

        $fixture = new ClassConstantIterator($constantMock);

        $this->assertEquals(10, $fixture->getLine());
    }

    /**
     * @covers ::getDocComment
     */
    public function testGetDocCommentPropFirst(): void
    {
        $const = m::mock(Const_::class);
        $classConstants = m::mock(ClassConst::class);
        $classConstants->consts = [$const];

        $const->shouldReceive('getDocComment')->once()->andReturn(new Doc('test'));
        $classConstants->shouldReceive('getDocComment')->never();

        $fixture = new ClassConstantIterator($classConstants);

        $this->assertEquals('test', $fixture->getDocComment()->getText());
    }

    /**
     * @covers ::getDocComment
     */
    public function testGetDocComment(): void
    {
        $const = m::mock(Const_::class);
        $classConstants = m::mock(ClassConst::class);
        $classConstants->consts = [$const];

        $const->shouldReceive('getDocComment')->once()->andReturnNull();
        $classConstants->shouldReceive('getDocComment')->once()->andReturn(new Doc('test'));

        $fixture = new ClassConstantIterator($classConstants);

        $this->assertEquals('test', $fixture->getDocComment()->getText());
    }
}
