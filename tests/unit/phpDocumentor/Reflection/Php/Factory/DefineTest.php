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
use phpDocumentor\Reflection\Php\Constant as ConstantDescriptor;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Comment\Doc;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Exit_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use stdClass;

/**
 * @uses   \phpDocumentor\Reflection\Php\ProjectFactoryStrategies
 * @uses   \phpDocumentor\Reflection\Php\Constant
 * @uses   \phpDocumentor\Reflection\Php\Visibility
 *
 * @covers \phpDocumentor\Reflection\Php\Factory\Define
 * @covers \phpDocumentor\Reflection\Php\Factory\AbstractFactory
 */
final class DefineTest extends TestCase
{
    protected function setUp() : void
    {
        $this->fixture = new Define(new PrettyPrinter());
    }

    public function testMatches() : void
    {
        $invalidExpressionType = new Expression(new Exit_());
        $invalidFunctionCall = new Expression(new FuncCall(new Name('print')));

        $this->assertFalse($this->fixture->matches(new stdClass()));
        $this->assertFalse($this->fixture->matches($invalidExpressionType));
        $this->assertFalse($this->fixture->matches($invalidFunctionCall));
        $this->assertTrue($this->fixture->matches($this->buildDefineStub()));
    }

    public function testCreate() : void
    {
        $constantStub = $this->buildDefineStub();

        /** @var ConstantDescriptor $constant */
        $constant = $this->fixture->create(
            $constantStub,
            new ProjectFactoryStrategies([]),
            new Context('Space\\MyClass')
        );

        $this->assertConstant($constant, '');
    }

    public function testCreateNamespace() : void
    {
        $constantStub = $this->buildDefineStub('\\OtherSpace\\MyClass');

        /** @var ConstantDescriptor $constant */
        $constant = $this->fixture->create(
            $constantStub,
            new ProjectFactoryStrategies([]),
            new Context('Space\\MyClass')
        );

        $this->assertConstant($constant, '\\OtherSpace\\MyClass');
    }

    public function testCreateGlobal() : void
    {
        $constantStub = $this->buildDefineStub();

        /** @var ConstantDescriptor $constant */
        $constant = $this->fixture->create(
            $constantStub,
            new ProjectFactoryStrategies([]),
            new Context('')
        );

        $this->assertConstant($constant, '');
    }

    public function testCreateWithDocBlock() : void
    {
        $doc = m::mock(Doc::class);
        $docBlock = new DocBlockDescriptor('');

        $constantStub = new Expression(
            new FuncCall(
                new Name('define'),
                [
                    new Arg(new String_('MY_CONST1')),
                    new Arg(new String_('a')),
                ]
            ),
            ['comments' => [$doc]]
        );
        $context = new Context('Space\\MyClass');

        $strategyMock = m::mock(ProjectFactoryStrategy::class);
        $containerMock = m::mock(StrategyContainer::class);

        $strategyMock->shouldReceive('create')
            ->with($doc, $containerMock, $context)
            ->andReturn($docBlock);

        $containerMock->shouldReceive('findMatching')
            ->with($doc)
            ->andReturn($strategyMock);

        /** @var ConstantDescriptor $constant */
        $constant = $this->fixture->create(
            $constantStub,
            $containerMock,
            $context
        );

        $this->assertConstant($constant, '');
        $this->assertSame($docBlock, $constant->getDocBlock());
    }

    private function buildDefineStub($namespace = '') : Expression
    {
        return new Expression(
            new FuncCall(
                new Name('define'),
                [
                    new Arg(new String_($namespace ?  $namespace . '\\MY_CONST1' : 'MY_CONST1')),
                    new Arg(new String_('a')),
                ]
            )
        );
    }

    private function assertConstant(ConstantDescriptor $constant, string $namespace) : void
    {
        $this->assertInstanceOf(ConstantDescriptor::class, $constant);
        $this->assertEquals($namespace . '\\MY_CONST1', (string) $constant->getFqsen());
        $this->assertEquals('\'a\'', $constant->getValue());
        $this->assertEquals('public', (string) $constant->getVisibility());
    }
}
