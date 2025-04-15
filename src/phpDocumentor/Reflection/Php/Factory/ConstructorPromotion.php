<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory;

use OutOfBoundsException;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\AsyncVisibility;
use phpDocumentor\Reflection\Php\Class_ as ClassElement;
use phpDocumentor\Reflection\Php\Factory\Reducer\Reducer;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\Property;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Php\Visibility;
use PhpParser\Modifiers;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use Webmozart\Assert\Assert;

final class ConstructorPromotion extends AbstractFactory
{
    /** @param iterable<Reducer> $reducers */
    public function __construct(
        private readonly ProjectFactoryStrategy $methodStrategy,
        DocBlockFactoryInterface $docBlockFactory,
        private readonly PrettyPrinter $valueConverter,
        iterable $reducers = [],
    ) {
        parent::__construct($docBlockFactory, $reducers);
    }

    public function matches(ContextStack $context, object $object): bool
    {
        try {
            return $context->peek() instanceof ClassElement &&
                $object instanceof ClassMethod &&
                (string) ($object->name) === '__construct';
        } catch (OutOfBoundsException) {
            return false;
        }
    }

    /** @param ClassMethod $object */
    protected function doCreate(ContextStack $context, object $object, StrategyContainer $strategies): object|null
    {
        $this->methodStrategy->create($context, $object, $strategies);

        foreach ($object->params as $param) {
            if ($param->flags === 0) {
                continue;
            }

            $this->promoteParameterToProperty($context, $strategies, $param);
        }

        return $context->peek();
    }

    private function promoteParameterToProperty(ContextStack $context, StrategyContainer $strategies, Param $param): void
    {
        $methodContainer = $context->peek();
        Assert::isInstanceOf($methodContainer, ClassElement::class);
        Assert::isInstanceOf($param->var, Variable::class);

        $property = new Property(
            new Fqsen($methodContainer->getFqsen() . '::$' . (string) $param->var->name),
            $this->buildPropertyVisibilty($param->flags),
            $this->createDocBlock($param->getDocComment(), $context->getTypeContext()),
            $param->default !== null ? $this->valueConverter->prettyPrintExpr($param->default) : null,
            false,
            new Location($param->getLine()),
            new Location($param->getEndLine()),
            (new Type())->fromPhpParser($param->type),
            $this->readOnly($param->flags),
        );

        foreach ($this->reducers as $reducer) {
            $property = $reducer->reduce($context, $param, $strategies, $property);
        }

        if ($property === null) {
            return;
        }

        $methodContainer->addProperty($property);
    }

    private function buildPropertyVisibilty(int $flags): Visibility
    {
        if ((bool) ($flags & Modifiers::VISIBILITY_SET_MASK) !== false) {
            return new AsyncVisibility(
                $this->buildReadVisibility($flags),
                $this->buildWriteVisibility($flags),
            );
        }

        return $this->buildReadVisibility($flags);
    }

    private function buildReadVisibility(int $flags): Visibility
    {
        if ((bool) ($flags & Modifiers::PRIVATE) === true) {
            return new Visibility(Visibility::PRIVATE_);
        }

        if ((bool) ($flags & Modifiers::PROTECTED) === true) {
            return new Visibility(Visibility::PROTECTED_);
        }

        return new Visibility(Visibility::PUBLIC_);
    }

    private function buildWriteVisibility(int $flags): Visibility
    {
        if ((bool) ($flags & Modifiers::PRIVATE_SET) === true) {
            return new Visibility(Visibility::PRIVATE_);
        }

        if ((bool) ($flags & Modifiers::PROTECTED_SET) === true) {
            return new Visibility(Visibility::PROTECTED_);
        }

        return new Visibility(Visibility::PUBLIC_);
    }

    private function readOnly(int $flags): bool
    {
        return (bool) ($flags & Modifiers::READONLY) === true;
    }
}
