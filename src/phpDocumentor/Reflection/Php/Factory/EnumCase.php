<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory;

use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Enum_ as EnumElement;
use phpDocumentor\Reflection\Php\EnumCase as EnumCaseElement;
use phpDocumentor\Reflection\Php\Expression as ValueExpression;
use phpDocumentor\Reflection\Php\Expression\ExpressionPrinter;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Node\Stmt\EnumCase as EnumCaseNode;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;

use function assert;
use function is_string;

final class EnumCase extends AbstractFactory
{
    private PrettyPrinter $prettyPrinter;

    public function __construct(DocBlockFactoryInterface $docBlockFactory, PrettyPrinter $prettyPrinter)
    {
        parent::__construct($docBlockFactory);

        $this->prettyPrinter = $prettyPrinter;
    }

    public function matches(ContextStack $context, object $object): bool
    {
        return $object instanceof EnumCaseNode;
    }

    /**
     * @param EnumCaseNode $object
     */
    protected function doCreate(ContextStack $context, object $object, StrategyContainer $strategies): void
    {
        $docBlock = $this->createDocBlock($object->getDocComment(), $context->getTypeContext());
        $enum = $context->peek();
        assert($enum instanceof EnumElement);

        $enum->addCase(new EnumCaseElement(
            $object->getAttribute('fqsen'),
            $docBlock,
            new Location($object->getLine()),
            new Location($object->getEndLine()),
            $this->determineValue($object)
        ));
    }

    private function determineValue(EnumCaseNode $value): ?ValueExpression
    {
        $expression = $value->expr !== null ? $this->prettyPrinter->prettyPrintExpr($value->expr) : null;
        if ($expression === null) {
            return null;
        }

        if ($this->prettyPrinter instanceof ExpressionPrinter) {
            $expression = new ValueExpression($expression, $this->prettyPrinter->getParts());
        }

        if (is_string($expression)) {
            $expression = new ValueExpression($expression, []);
        }

        return $expression;
    }
}
