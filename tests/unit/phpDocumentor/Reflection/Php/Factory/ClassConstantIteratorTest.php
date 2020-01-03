<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Factory;

use Mockery as m;
use phpDocumentor\Reflection\Fqsen;
use PhpParser\Comment\Doc;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassConst;
use PHPUnit\Framework\TestCase;

/**
 * Class PropertyIteratorTest
 *
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\ClassConstantIterator
 */
class ClassConstantIteratorTest extends TestCase
{
    protected function tearDown() : void
    {
        m::close();
    }

    /**
     * @covers ::current()
     * @covers ::next()
     * @covers ::valid()
     * @covers ::rewind()
     * @covers ::getName()
     * @covers ::getFqsen()
     */
    public function testIterateProps() : void
    {
        $const1        = new Const_('\Space\MyClass::MY_CONST1', new Variable('a'));
        $const1->fqsen = new Fqsen((string) $const1->name);
        $const2        = new Const_('\Space\MyClass::MY_CONST2', new Variable('b'));
        $const2->fqsen = new Fqsen((string) $const2->name);

        $classConstantNode = new ClassConst([$const1, $const2]);

        $i = 1;
        foreach (new ClassConstantIterator($classConstantNode) as $constant) {
            $this->assertEquals('\Space\MyClass::MY_CONST' . $i, $constant->getName());
            $this->assertEquals('\Space\MyClass::MY_CONST' . $i, (string) $constant->getFqsen());

            ++$i;
        }
    }

    /**
     * @covers ::key()
     * @covers ::next()
     */
    public function testKey() : void
    {
        $propertyMock = m::mock(ClassConst::class);

        $fixture = new ClassConstantIterator($propertyMock);

        $this->assertEquals(0, $fixture->key());
        $fixture->next();
        $this->assertEquals(1, $fixture->key());
    }

    /**
     * @covers ::__construct
     * @covers ::getLine
     */
    public function testProxyMethods() : void
    {
        $propertyMock = m::mock(ClassConst::class);
        $propertyMock->shouldReceive('getLine')->once()->andReturn(10);

        $fixture = new ClassConstantIterator($propertyMock);

        $this->assertEquals(10, $fixture->getLine());
    }

    /**
     * @covers ::getDocComment
     */
    public function testGetDocCommentPropFirst() : void
    {
        $const                  = m::mock(Const_::class);
        $classConstants         = m::mock(ClassConst::class);
        $classConstants->consts = [$const];

        $const->shouldReceive('getDocComment')->once()->andReturn(new Doc('test'));
        $classConstants->shouldReceive('getDocComment')->never();

        $fixture = new ClassConstantIterator($classConstants);

        $this->assertEquals('test', $fixture->getDocComment()->getText());
    }

    /**
     * @covers ::getDocComment
     */
    public function testGetDocComment() : void
    {
        $const                  = m::mock(Const_::class);
        $classConstants         = m::mock(ClassConst::class);
        $classConstants->consts = [$const];

        $const->shouldReceive('getDocComment')->once()->andReturnNull();
        $classConstants->shouldReceive('getDocComment')->once()->andReturn(new Doc('test'));

        $fixture = new ClassConstantIterator($classConstants);

        $this->assertEquals('test', $fixture->getDocComment()->getText());
    }
}
