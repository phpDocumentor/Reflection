<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory\Reducer;

use phpDocumentor\Reflection\Php\Argument as ArgumentDescriptor;
use phpDocumentor\Reflection\Php\Factory\ContextStack;
use phpDocumentor\Reflection\Php\Factory\Type;
use phpDocumentor\Reflection\Php\Function_;
use phpDocumentor\Reflection\Php\Method;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\FunctionLike;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use Webmozart\Assert\Assert;

use function is_string;

class Parameter implements Reducer
{
    public function __construct(private readonly PrettyPrinter $valueConverter)
    {
    }

    public function reduce(
        ContextStack $context,
        object $object,
        StrategyContainer $strategies,
        object|null $carry,
    ): object|null {
        if ($object instanceof FunctionLike === false) {
            return $carry;
        }

        if ($carry instanceof Method === false && $carry instanceof Function_ === false) {
            return null;
        }

        foreach ($object->getParams() as $param) {
            Assert::isInstanceOf($param->var, Variable::class);

            $carry->addArgument(
                new ArgumentDescriptor(
                    is_string($param->var->name) ? $param->var->name : $this->valueConverter->prettyPrintExpr($param->var->name),
                    (new Type())->fromPhpParser($param->type),
                    $param->default !== null ? $this->valueConverter->prettyPrintExpr($param->default) : null,
                    $param->byRef,
                    $param->variadic,
                ),
            );
        }

        return $carry;
    }
}
