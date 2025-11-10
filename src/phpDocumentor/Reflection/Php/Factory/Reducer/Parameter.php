<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory\Reducer;

use Override;
use phpDocumentor\Reflection\Php\Argument as ArgumentDescriptor;
use phpDocumentor\Reflection\Php\Expression;
use phpDocumentor\Reflection\Php\Expression\ExpressionPrinter;
use phpDocumentor\Reflection\Php\Factory\ContextStack;
use phpDocumentor\Reflection\Php\Factory\Type;
use phpDocumentor\Reflection\Php\Function_;
use phpDocumentor\Reflection\Php\Method;
use phpDocumentor\Reflection\Php\PropertyHook;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Param;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use Webmozart\Assert\Assert;

use function is_string;

class Parameter implements Reducer
{
    public function __construct(private readonly PrettyPrinter $valueConverter)
    {
    }

    #[Override]
    public function reduce(
        ContextStack $context,
        object $object,
        StrategyContainer $strategies,
        object|null $carry,
    ): object|null {
        if ($object instanceof FunctionLike === false) {
            return $carry;
        }

        if ($carry instanceof Method === false && $carry instanceof Function_ === false && $carry instanceof PropertyHook === false) {
            return null;
        }

        foreach ($object->getParams() as $param) {
            Assert::isInstanceOf($param->var, Variable::class);

            $carry->addArgument(
                new ArgumentDescriptor(
                    is_string($param->var->name) ? $param->var->name : $this->valueConverter->prettyPrintExpr($param->var->name),
                    (new Type())->fromPhpParser($param->type),
                    $this->determineDefault($param, $context->getTypeContext()),
                    $param->byRef,
                    $param->variadic,
                ),
            );
        }

        return $carry;
    }

    private function determineDefault(Param $value, Context|null $context): Expression|null
    {
        if ($this->valueConverter instanceof ExpressionPrinter) {
            $expression = $value->default !== null ? $this->valueConverter->prettyPrintExpr($value->default, $context) : null;
        } else {
            $expression = $value->default !== null ? $this->valueConverter->prettyPrintExpr($value->default) : null;
        }

        if ($expression === null) {
            return null;
        }

        if ($this->valueConverter instanceof ExpressionPrinter) {
            $expression = new Expression($expression, $this->valueConverter->getParts());
        }

        if (is_string($expression)) {
            $expression = new Expression($expression, []);
        }

        return $expression;
    }
}
