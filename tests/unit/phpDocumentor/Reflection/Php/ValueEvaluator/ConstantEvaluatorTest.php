<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\ValueEvaluator;

use phpDocumentor\Reflection\Php\Factory\ContextStack;
use phpDocumentor\Reflection\Php\Project;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\ConstExprEvaluationException;
use PhpParser\Node\Expr\ShellExec;
use PhpParser\Node\Scalar\MagicConst\Namespace_;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\Php\ValueEvaluator\ConstantEvaluator
 */
final class ConstantEvaluatorTest extends TestCase
{
    /** @covers ::evaluate */

    /** @covers ::evaluateFallback */
    public function testEvaluateThrowsWhenTypeContextIsNotSet(): void
    {
        $this->expectException(ConstExprEvaluationException::class);

        $evaluator = new ConstantEvaluator();
        $evaluator->evaluate(new Namespace_(), new ContextStack(new Project('test')));
    }

    /** @covers ::evaluate */

    /** @covers ::evaluateFallback */
    public function testEvaluateThrowsOnUnknownExpression(): void
    {
        $this->expectException(ConstExprEvaluationException::class);

        $evaluator = new ConstantEvaluator();
        $result = $evaluator->evaluate(new ShellExec([]), new ContextStack(new Project('test'), new Context('Test')));
    }

    /** @covers ::evaluate */

    /** @covers ::evaluateFallback */
    public function testEvaluateReturnsNamespaceFromContext(): void
    {
        $evaluator = new ConstantEvaluator();
        $result = $evaluator->evaluate(new Namespace_(), new ContextStack(new Project('test'), new Context('Test')));

        self::assertSame('Test', $result);
    }
}
