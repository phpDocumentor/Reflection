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

use phpDocumentor\Reflection\Exception;
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
    public function __construct($strategies)
    {
        foreach ($strategies as $strategy) {
            if (!$strategy instanceof ProjectFactoryStrategy) {
                throw new \InvalidArgumentException(
                    sprintf(
                        '%s is not implementing %s',
                        get_class($strategy),
                        ProjectFactoryStrategy::class
                    )
                );
            }
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

        throw new Exception(
            sprintf(
                'No matching factory found for %s',
                is_object($object) ? get_class($object) : gettype($object)
            )
        );
    }
}