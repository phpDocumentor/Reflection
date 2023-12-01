<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory;

use OutOfBoundsException;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Class_ as ClassElement;
use phpDocumentor\Reflection\Php\Expression;
use phpDocumentor\Reflection\Php\Expression\ExpressionPrinter;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\Property;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Php\Visibility;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use Webmozart\Assert\Assert;

use function is_string;

final class ConstructorPromotion extends AbstractFactory
{
    private PrettyPrinter $valueConverter;
    private ProjectFactoryStrategy $methodStrategy;

    public function __construct(
        ProjectFactoryStrategy $methodStrategy,
        DocBlockFactoryInterface $docBlockFactory,
        PrettyPrinter $prettyPrinter
    ) {
        parent::__construct($docBlockFactory);
        $this->valueConverter = $prettyPrinter;
        $this->methodStrategy = $methodStrategy;
    }

    public function matches(ContextStack $context, object $object): bool
    {
        try {
            return $context->peek() instanceof ClassElement &&
                $object instanceof ClassMethod &&
                (string) ($object->name) === '__construct';
        } catch (OutOfBoundsException $e) {
            return false;
        }
    }

    /**
     * @param ClassMethod $object
     */
    protected function doCreate(ContextStack $context, object $object, StrategyContainer $strategies): void
    {
        $this->methodStrategy->create($context, $object, $strategies);

        foreach ($object->params as $param) {
            if ($param->flags === 0) {
                continue;
            }

            $this->promoteParameterToProperty($context, $param);
        }
    }

    private function promoteParameterToProperty(ContextStack $context, Param $param): void
    {
        $methodContainer = $context->peek();
        Assert::isInstanceOf($methodContainer, ClassElement::class);
        Assert::isInstanceOf($param->var, Variable::class);

        $property = new Property(
            new Fqsen($methodContainer->getFqsen() . '::$' . (string) $param->var->name),
            $this->buildPropertyVisibilty($param->flags),
            $this->createDocBlock($param->getDocComment(), $context->getTypeContext()),
            $this->determineDefault($param),
            false,
            new Location($param->getLine()),
            new Location($param->getEndLine()),
            (new Type())->fromPhpParser($param->type),
            $this->readOnly($param->flags)
        );

        $methodContainer->addProperty($property);
    }

    private function determineDefault(Param $value): ?Expression
    {
        $expression = $value->default !== null ? $this->valueConverter->prettyPrintExpr($value->default) : null;
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

    private function buildPropertyVisibilty(int $flags): Visibility
    {
        if ((bool) ($flags & Class_::MODIFIER_PRIVATE) === true) {
            return new Visibility(Visibility::PRIVATE_);
        }

        if ((bool) ($flags & Class_::MODIFIER_PROTECTED) === true) {
            return new Visibility(Visibility::PROTECTED_);
        }

        return new Visibility(Visibility::PUBLIC_);
    }

    private function readOnly(int $flags): bool
    {
        return (bool) ($flags & Class_::MODIFIER_READONLY) === true;
    }
}
