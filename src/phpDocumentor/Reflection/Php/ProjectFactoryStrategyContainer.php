<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2015 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;

final class ProjectFactoryStrategyContainer implements StrategyContainer
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
     * @throws Exception when no matching strategy was found.
     */
    public function findMatching($object)
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->matches($object)) {
                return $strategy;
            }
        }

        throw new \OutOfBoundsException(
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