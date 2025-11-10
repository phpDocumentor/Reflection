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

use Override;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Constant as ConstantElement;
use phpDocumentor\Reflection\Php\Expression as ValueExpression;
use phpDocumentor\Reflection\Php\Expression\ExpressionPrinter;
use phpDocumentor\Reflection\Php\File as FileElement;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Php\ValueEvaluator\ConstantEvaluator;
use PhpParser\ConstExprEvaluationException;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\VariadicPlaceholder;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;

use function assert;
use function is_string;
use function sprintf;
use function str_starts_with;

/**
 * Strategy to convert `define` expressions to ConstantElement
 *
 * @see ConstantElement
 * @see GlobalConstantIterator
 */
final class Define extends AbstractFactory
{
    /**
     * Initializes the object.
     */
    public function __construct(
        DocBlockFactoryInterface $docBlockFactory,
        private readonly PrettyPrinter $valueConverter,
        private readonly ConstantEvaluator $constantEvaluator = new ConstantEvaluator(),
    ) {
        parent::__construct($docBlockFactory);
    }

    #[Override]
    public function matches(ContextStack $context, object $object): bool
    {
        if (!$object instanceof Expression) {
            return false;
        }

        $expression = $object->expr;
        if (!$expression instanceof FuncCall) {
            return false;
        }

        if (!$expression->name instanceof Name) {
            return false;
        }

        return (string) $expression->name === 'define';
    }

    /**
     * Creates an Constant out of the given object.
     *
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param Expression $object object to convert to an Element
     * @param StrategyContainer $strategies used to convert nested objects.
     */
    #[Override]
    protected function doCreate(
        ContextStack $context,
        object $object,
        StrategyContainer $strategies,
    ): object|null {
        $expression = $object->expr;
        assert($expression instanceof FuncCall);

        [$name, $value] = $expression->args;

        //We cannot calculate the name of a variadic consuming define.
        if ($name instanceof VariadicPlaceholder || $value instanceof VariadicPlaceholder) {
            return null;
        }

        $file = $context->search(FileElement::class);
        assert($file instanceof FileElement);

        $fqsen = $this->determineFqsen($name, $context);
        if ($fqsen === null) {
            return null;
        }

        $constant = new ConstantElement(
            $fqsen,
            $this->createDocBlock($object->getDocComment(), $context->getTypeContext()),
            $this->determineValue($value),
            new Location($object->getLine()),
            new Location($object->getEndLine()),
        );

        $file->addConstant($constant);

        return $constant;
    }

    private function determineValue(Arg|null $value): ValueExpression|null
    {
        if ($value === null) {
            return null;
        }

        $expression = $this->valueConverter->prettyPrintExpr($value->value);
        if ($this->valueConverter instanceof ExpressionPrinter) {
            $expression = new ValueExpression($expression, $this->valueConverter->getParts());
        }

        if (is_string($expression)) {
            $expression = new ValueExpression($expression, []);
        }

        return $expression;
    }

    private function determineFqsen(Arg $name, ContextStack $context): Fqsen|null
    {
        return $this->fqsenFromExpression($name->value, $context);
    }

    private function fqsenFromExpression(Expr $nameString, ContextStack $context): Fqsen|null
    {
        try {
            return $this->fqsenFromString($this->constantEvaluator->evaluate($nameString, $context));
        } catch (ConstExprEvaluationException) {
            //Ignore any errors as we cannot evaluate all expressions
            return null;
        }
    }

    private function fqsenFromString(string $nameString): Fqsen
    {
        if (str_starts_with($nameString, '\\') === false) {
            return new Fqsen(sprintf('\\%s', $nameString));
        }

        return new Fqsen(sprintf('%s', $nameString));
    }
}
