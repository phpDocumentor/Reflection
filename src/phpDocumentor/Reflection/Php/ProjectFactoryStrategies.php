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

namespace phpDocumentor\Reflection\Php;

use OutOfBoundsException;
use Override;
use phpDocumentor\Reflection\Php\Factory\ContextStack;
use SplPriorityQueue;

use function get_debug_type;
use function sprintf;

final class ProjectFactoryStrategies implements StrategyContainer
{
    public const DEFAULT_PRIORITY = 1000;

    /** @var SplPriorityQueue<int, ProjectFactoryStrategy> */
    private readonly SplPriorityQueue $strategies;

    /**
     * Initializes the factory with a number of strategies.
     *
     * @param ProjectFactoryStrategy[] $strategies
     */
    public function __construct(array $strategies)
    {
        $this->strategies = new SplPriorityQueue();
        foreach ($strategies as $strategy) {
            $this->addStrategy($strategy);
        }
    }

    /**
     * Find the ProjectFactoryStrategy that matches $object.
     *
     * @throws OutOfBoundsException When no matching strategy was found.
     */
    #[Override]
    public function findMatching(ContextStack $context, mixed $object): ProjectFactoryStrategy
    {
        foreach (clone $this->strategies as $strategy) {
            if ($strategy->matches($context, $object)) {
                return $strategy;
            }
        }

        throw new OutOfBoundsException(
            sprintf(
                'No matching factory found for %s',
                get_debug_type($object),
            ),
        );
    }

    /**
     * Add a strategy to this container.
     */
    public function addStrategy(ProjectFactoryStrategy $strategy, int $priority = self::DEFAULT_PRIORITY): void
    {
        $this->strategies->insert($strategy, $priority);
    }
}
