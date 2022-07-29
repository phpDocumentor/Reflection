<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\ValueEvaluator;

use phpDocumentor\Reflection\Php\Factory\ContextStack;
use PhpParser\ConstExprEvaluationException;
use PhpParser\ConstExprEvaluator;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\MagicConst\Namespace_;

use function sprintf;

/**
 * @internal
 */
final class ConstantEvaluator
{
    /** @throws ConstExprEvaluationException */
    public function evaluate(Expr $expr, ContextStack $contextStack): string
    {
        // @codeCoverageIgnoreStart
        $evaluator = new ConstExprEvaluator(fn (Expr $expr): string => $this->evaluateFallback($expr, $contextStack));

        return $evaluator->evaluateSilently($expr);
        // @codeCoverageIgnoreEnd
    }

    /** @throws ConstExprEvaluationException */
    private function evaluateFallback(Expr $expr, ContextStack $contextStack): string
    {
        $typeContext = $contextStack->getTypeContext();
        if ($typeContext === null) {
            throw new ConstExprEvaluationException(
                sprintf('Expression of type %s cannot be evaluated', $expr->getType())
            );
        }

        if ($expr instanceof Namespace_) {
            return $typeContext->getNamespace();
        }

        throw new ConstExprEvaluationException(
            sprintf('Expression of type %s cannot be evaluated', $expr->getType())
        );
    }
}
