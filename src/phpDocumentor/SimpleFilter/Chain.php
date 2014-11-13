<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\SimpleFilter;

/**
 * A chain of filters that can be used to store and order a series of filters based on a given priority.
 */
final class Chain implements FilterInterface, \Countable, \IteratorAggregate
{
    /** @var \SplPriorityQueue  */
    private $innerQueue;

    /**
     * Initializes this chain with its dependencies.
     */
    public function __construct()
    {
        $this->innerQueue = new \SplPriorityQueue;
    }

    /**
     * Attaches a filter or callback to the queue.
     *
     * @param FilterInterface|callable $filter
     * @param integer                  $priority The position in the queue where each filter is executed; filters are
     *   executed from lowest to highest number.
     *
     * @throws \InvalidArgumentException if the provided filter is not a callable or Filter object.
     *
     * @return $this
     */
    public function attach($filter, $priority = 1000)
    {
        if ($filter instanceof FilterInterface) {
            $filter = array($filter, 'filter');
        }

        if (!is_callable($filter)) {
            throw new \InvalidArgumentException(
                'Expected an object implementing the FilterInterface or a valid callback, received: ' . gettype($filter)
            );
        }

        $this->innerQueue->insert($filter, $priority);

        return $this;
    }

    /**
     * Passes the provided value to each filter in order of the priority in which the filters were attached and returns
     * the filtered value.
     *
     * @param mixed $value
     *
     * @throws \RuntimeException if, for some reason, one of the filters is not callable. The `attach()` method should
     *   prevent this but it is added as a sanity check.
     *
     * @return mixed the value after it has been passed through each filter (and thus possibly modified or even
     *     replaced with a null value).
     */
    public function filter($value)
    {
        foreach ($this as $filterCallback) {
            if (!is_callable($filterCallback)) {
                throw new \RuntimeException(
                    'Unable to process filter, one of the filters is not a FilterInterface or callback'
                );
            }

            $value = $filterCallback($value);
        }

        return $value;
    }

    /**
     * Returns the number of filters associated with this Chain.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->innerQueue);
    }

    /**
     * Returns a traversable representation of the filters in this chain.
     *
     * @see self::filter() to automatically filter a value through each of the child filters.
     *
     * @return \SplPriorityQueue|\Traversable
     */
    public function getIterator()
    {
        // the clone is done because an SplPriorityQueue removes items from the queue when iterating through it, but
        // we want to be able to always re-iterate a filter chain.
        return clone $this->innerQueue;
    }
}