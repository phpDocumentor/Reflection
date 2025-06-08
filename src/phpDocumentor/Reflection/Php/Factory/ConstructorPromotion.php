<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory;

use OutOfBoundsException;
use Override;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Class_ as ClassElement;
use phpDocumentor\Reflection\Php\Factory\Reducer\Reducer;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
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

    #[Override]
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
    #[Override]
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

        $property = PropertyBuilder::create(
            $this->valueConverter,
            $this->docBlockFactory,
            $strategies,
            $this->reducers,
        )->fqsen(new Fqsen($methodContainer->getFqsen() . '::$' . (string) $param->var->name))
            ->visibility($param)
            ->type($param->type)
            ->docblock($param->getDocComment())
            ->default($param->default)
            ->readOnly($this->readOnly($param->flags))
            ->static(false)
            ->startLocation(new Location($param->getLine()))
            ->endLocation(new Location($param->getEndLine()))
            ->hooks($param->hooks ?? [])
            ->build($context);

        foreach ($this->reducers as $reducer) {
            $property = $reducer->reduce($context, $param, $strategies, $property);
        }

        if ($property === null) {
            return;
        }

        $methodContainer->addProperty($property);
    }

    private function readOnly(int $flags): bool
    {
        return (bool) ($flags & Modifiers::READONLY) === true;
    }
}
