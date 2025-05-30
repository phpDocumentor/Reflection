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

use InvalidArgumentException;
use Override;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Php\Factory\Reducer\Reducer;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Comment\Doc;
use PhpParser\NodeAbstract;

use function get_debug_type;
use function sprintf;

abstract class AbstractFactory implements ProjectFactoryStrategy
{
    /** @param iterable<Reducer> $reducers */
    public function __construct(
        protected readonly DocBlockFactoryInterface $docBlockFactory,
        protected readonly iterable $reducers = [],
    ) {
    }

    /**
     * Returns true when the strategy is able to handle the object.
     *
     * @param object $object object to check.
     */
    #[Override]
    abstract public function matches(ContextStack $context, object $object): bool;

    #[Override]
    public function create(ContextStack $context, object $object, StrategyContainer $strategies): void
    {
        if (!$this->matches($context, $object)) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s cannot handle objects with the type %s',
                    self::class,
                    get_debug_type($object),
                ),
            );
        }

        $element = $this->doCreate($context, $object, $strategies);
        foreach ($this->reducers as $reducer) {
            $element = $reducer->reduce($context, $object, $strategies, $element);
        }
    }

    /**
     * Creates an Element out of the given object.
     *
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param NodeAbstract|object $object object to convert to an Element
     */
    abstract protected function doCreate(ContextStack $context, object $object, StrategyContainer $strategies): object|null;

    protected function createDocBlock(Doc|null $docBlock = null, Context|null $context = null): DocBlock|null
    {
        if ($docBlock === null) {
            return null;
        }

        return $this->docBlockFactory->create($docBlock->getText(), $context);
    }
}
