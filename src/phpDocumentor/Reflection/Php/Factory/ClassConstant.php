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
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\Constant as ConstantElement;
use phpDocumentor\Reflection\Php\Enum_;
use phpDocumentor\Reflection\Php\Expression;
use phpDocumentor\Reflection\Php\Expression\ExpressionPrinter;
use phpDocumentor\Reflection\Php\Factory\Reducer\Reducer;
use phpDocumentor\Reflection\Php\Interface_;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Php\Trait_;
use phpDocumentor\Reflection\Php\Visibility;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use Webmozart\Assert\Assert;

use function is_string;

/**
 * Strategy to convert ClassConstantIterator to ConstantElement
 *
 * @see ConstantElement
 * @see ClassConstantIterator
 */
final class ClassConstant extends AbstractFactory
{
    /** @param iterable<Reducer> $reducers */
    public function __construct(
        DocBlockFactoryInterface $blockFactory,
        private readonly PrettyPrinter $valueConverter,
        iterable $reducers = [],
    ) {
        parent::__construct($blockFactory, $reducers);
    }

    #[Override]
    public function matches(ContextStack $context, object $object): bool
    {
        return $object instanceof ClassConst;
    }

    /**
     * Creates an Constant out of the given object.
     *
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param ContextStack $context of the created object
     * @param ClassConst $object object to convert to an Element
     * @param StrategyContainer $strategies used to convert nested objects.
     */
    #[Override]
    protected function doCreate(
        ContextStack $context,
        object $object,
        StrategyContainer $strategies,
    ): object|null {
        $constantContainer = $context->peek();
        Assert::isInstanceOfAny(
            $constantContainer,
            [
                Class_::class,
                Enum_::class,
                Interface_::class,
                Trait_::class,
            ],
        );

        $constants = new ClassConstantIterator($object);

        foreach ($constants as $const) {
            $constant = new ConstantElement(
                $const->getFqsen(),
                $this->createDocBlock($const->getDocComment(), $context->getTypeContext()),
                $this->determineValue($const),
                new Location($const->getLine()),
                new Location($const->getEndLine()),
                $this->buildVisibility($const),
                $const->isFinal(),
            );

            foreach ($this->reducers as $reducer) {
                $constant = $reducer->reduce($context, $const, $strategies, $constant);
            }

            if ($constant === null) {
                continue;
            }

            $constantContainer->addConstant($constant);
        }

        return null;
    }

    private function determineValue(ClassConstantIterator $value): Expression
    {
        $expression = $this->valueConverter->prettyPrintExpr($value->getValue());
        if ($this->valueConverter instanceof ExpressionPrinter) {
            $expression = new Expression($expression, $this->valueConverter->getParts());
        }

        if (is_string($expression)) {
            $expression = new Expression($expression, []);
        }

        return $expression;
    }

    /**
     * Converts the visibility of the constant to a valid Visibility object.
     */
    private function buildVisibility(ClassConstantIterator $node): Visibility
    {
        if ($node->isPrivate()) {
            return new Visibility(Visibility::PRIVATE_);
        }

        if ($node->isProtected()) {
            return new Visibility(Visibility::PROTECTED_);
        }

        return new Visibility(Visibility::PUBLIC_);
    }
}
