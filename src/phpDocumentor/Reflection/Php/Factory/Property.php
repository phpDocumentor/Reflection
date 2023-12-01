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

use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\Expression;
use phpDocumentor\Reflection\Php\Expression\ExpressionPrinter;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\Property as PropertyDescriptor;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Php\Trait_;
use phpDocumentor\Reflection\Php\Visibility;
use PhpParser\Node\Stmt\Property as PropertyNode;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use Webmozart\Assert\Assert;

use function is_string;

/**
 * Strategy to convert PropertyIterator to PropertyDescriptor
 *
 * @see PropertyDescriptor
 * @see PropertyIterator
 */
final class Property extends AbstractFactory implements ProjectFactoryStrategy
{
    private PrettyPrinter $valueConverter;

    /**
     * Initializes the object.
     */
    public function __construct(DocBlockFactoryInterface $docBlockFactory, PrettyPrinter $prettyPrinter)
    {
        $this->valueConverter = $prettyPrinter;
        parent::__construct($docBlockFactory);
    }

    public function matches(ContextStack $context, object $object): bool
    {
        return $object instanceof PropertyNode;
    }

    /**
     * Creates an PropertyDescriptor out of the given object.
     *
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param ContextStack $context used to convert nested objects.
     * @param PropertyNode $object
     */
    protected function doCreate(
        ContextStack $context,
        object $object,
        StrategyContainer $strategies
    ): void {
        $propertyContainer = $context->peek();
        Assert::isInstanceOfAny(
            $propertyContainer,
            [
                Class_::class,
                Trait_::class,
            ]
        );

        $iterator = new PropertyIterator($object);
        foreach ($iterator as $stmt) {
            $propertyContainer->addProperty(
                new PropertyDescriptor(
                    $stmt->getFqsen(),
                    $this->buildVisibility($stmt),
                    $this->createDocBlock($stmt->getDocComment(), $context->getTypeContext()),
                    $this->determineDefault($stmt),
                    $stmt->isStatic(),
                    new Location($stmt->getLine()),
                    new Location($stmt->getEndLine()),
                    (new Type())->fromPhpParser($stmt->getType()),
                    $stmt->isReadonly()
                )
            );
        }
    }

    private function determineDefault(PropertyIterator $value): ?Expression
    {
        $default = $value->getDefault();
        $expression = $default !== null ? $this->valueConverter->prettyPrintExpr($default) : null;
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

    /**
     * Converts the visibility of the property to a valid Visibility object.
     */
    private function buildVisibility(PropertyIterator $node): Visibility
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
