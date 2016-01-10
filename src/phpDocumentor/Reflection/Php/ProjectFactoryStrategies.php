<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php;

use OutOfBoundsException;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;

final class ProjectFactoryStrategies implements StrategyContainer
{
    /**
     * @var ProjectFactoryStrategy[]
     */
    private $strategies;

    /**
     * Initializes the factory with a number of strategies.
     *
     * @param ProjectFactoryStrategy[] $strategies
     */
    public function __construct(array $strategies)
    {
        foreach ($strategies as $strategy) {
            $this->addStrategy($strategy);
        }

        $this->strategies = $strategies;
    }


    /**
     * Find the ProjectFactoryStrategy that matches $object.
     *
     * @param mixed $object
     * @return ProjectFactoryStrategy
     * @throws OutOfBoundsException when no matching strategy was found.
     */
    public function findMatching($object)
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->matches($object)) {
                return $strategy;
            }
        }

        throw new OutOfBoundsException(
            sprintf(
                'No matching factory found for %s',
                is_object($object) ? get_class($object) : gettype($object)
            )
        );
    }

    /**
     * Add a strategy to this container.
     *
     * @param ProjectFactoryStrategy $strategy
     */
    public function addStrategy(ProjectFactoryStrategy $strategy)
    {
        $this->strategies[] = $strategy;
    }
}
