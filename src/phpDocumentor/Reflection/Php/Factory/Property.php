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
use phpDocumentor\Reflection\Php\Factory\Reducer\Reducer;
use phpDocumentor\Reflection\Php\Property as PropertyDescriptor;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Php\Trait_;
use PhpParser\Node\Stmt\Property as PropertyNode;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use Webmozart\Assert\Assert;

/**
 * Strategy to convert PropertyIterator to PropertyDescriptor
 *
 * @see PropertyDescriptor
 * @see PropertyIterator
 */
final class Property extends AbstractFactory
{
    /** @param iterable<Reducer> $reducers */
    public function __construct(
        DocBlockFactoryInterface $docBlockFactory,
        private readonly PrettyPrinter $valueConverter,
        iterable $reducers = [],
    ) {
        parent::__construct($docBlockFactory, $reducers);
    }

    #[Override]
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
    #[Override]
    protected function doCreate(
        ContextStack $context,
        object $object,
        StrategyContainer $strategies,
    ): object|null {
        $propertyContainer = $context->peek();
        Assert::isInstanceOfAny(
            $propertyContainer,
            [
                Class_::class,
                Trait_::class,
            ],
        );

        $iterator = new PropertyIterator($object);
        foreach ($iterator as $stmt) {
            $property = PropertyBuilder::create(
                $this->valueConverter,
                $this->docBlockFactory,
                $strategies,
                $this->reducers,
            )
                ->fqsen($stmt->getFqsen())
                ->visibility($stmt)
                ->type($stmt->getType())
                ->docblock($stmt->getDocComment())
                ->default($stmt->getDefault())
                ->static($stmt->isStatic())
                ->startLocation(new Location($stmt->getLine()))
                ->endLocation(new Location($stmt->getEndLine()))
                ->readOnly($stmt->isReadonly())
                ->hooks($stmt->getHooks())
                ->build($context);

            foreach ($this->reducers as $reducer) {
                $property = $reducer->reduce($context, $object, $strategies, $property);
            }

            if ($property === null) {
                continue;
            }

            $propertyContainer->addProperty($property);
        }

        return null;
    }
}
