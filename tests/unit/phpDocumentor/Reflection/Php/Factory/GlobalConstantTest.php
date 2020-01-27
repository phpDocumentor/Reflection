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
use phpDocumentor\Reflection\Php\Constant as ConstantDescriptor;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Comment\Doc;
use PhpParser\Node\Const_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Const_ as ConstStatement;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use stdClass;

/**
 * @uses   \phpDocumentor\Reflection\Php\Factory\GlobalConstantIterator
 * @uses   \phpDocumentor\Reflection\Php\ProjectFactoryStrategies
 * @uses   \phpDocumentor\Reflection\Php\Constant
 * @uses   \phpDocumentor\Reflection\Php\Visibility
 *
 * @covers \phpDocumentor\Reflection\Php\Factory\GlobalConstant
 * @covers \phpDocumentor\Reflection\Php\Factory\AbstractFactory
 */
final class GlobalConstantTest extends TestCase
{
    protected function setUp() : void
    {
        $this->fixture = new GlobalConstant(new PrettyPrinter());
    }

    public function testMatches() : void
    {
        $this->assertFalse($this->fixture->matches(new stdClass()));
        $this->assertTrue($this->fixture->matches($this->buildConstantIteratorStub()));
    }

    public function testCreate() : void
    {
        $factory = new ProjectFactoryStrategies([]);

        $constantStub = $this->buildConstantIteratorStub();

        /** @var ConstantDescriptor $constant */
        $constant = $this->fixture->create($constantStub, $factory);

        $this->assertConstant($constant);
    }

    public function testCreateWithDocBlock() : void
    {
        $doc = m::mock(Doc::class);
        $docBlock = new DocBlockDescriptor('');

        $const = new Const_('\Space\MyClass\MY_CONST1', new String_('a'), ['comments' => [$doc]]);
        $const->fqsen = new Fqsen((string) $const->name);

        $constantStub = new GlobalConstantIterator(new ConstStatement([$const]));
        $strategyMock = m::mock(ProjectFactoryStrategy::class);
        $containerMock = m::mock(StrategyContainer::class);

        $strategyMock->shouldReceive('create')
            ->with($doc, $containerMock, null)
            ->andReturn($docBlock);

        $containerMock->shouldReceive('findMatching')
            ->with($doc)
            ->andReturn($strategyMock);

        /** @var ConstantDescriptor $constant */
        $constant = $this->fixture->create($constantStub, $containerMock);

        $this->assertConstant($constant);
        $this->assertSame($docBlock, $constant->getDocBlock());
    }

    private function buildConstantIteratorStub() : GlobalConstantIterator
    {
        $const = new Const_('\Space\MyClass\MY_CONST1', new String_('a'));
        $const->fqsen = new Fqsen((string) $const->name);

        return new GlobalConstantIterator(new ConstStatement([$const]));
    }

    private function assertConstant(ConstantDescriptor $constant) : void
    {
        $this->assertInstanceOf(ConstantDescriptor::class, $constant);
        $this->assertEquals('\Space\MyClass\MY_CONST1', (string) $constant->getFqsen());
        $this->assertEquals('\'a\'', $constant->getValue());
        $this->assertEquals('public', (string) $constant->getVisibility());
    }
}
