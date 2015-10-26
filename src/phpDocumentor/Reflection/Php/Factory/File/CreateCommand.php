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

namespace phpDocumentor\Reflection\Php\Factory\File;


use phpDocumentor\Reflection\Php\StrategyContainer;

/**
 * File Create command is used by the File Factory Strategy.
 * The command is passed to the registered middle ware classes.
 */
final class CreateCommand
{
    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @var string
     */
    private $object;

    /**
     * @var StrategyContainer
     */
    private $strategies;

    /**
     * Initializes this command.
     *
     * @param Adapter $adapter
     * @param string $object
     * @param StrategyContainer $strategies
     */
    public function __construct(Adapter $adapter, $object, StrategyContainer $strategies)
    {

        $this->adapter = $adapter;
        $this->object = $object;
        $this->strategies = $strategies;
    }

    /**
     * Returns the adapter used by the command handler for this command.
     *
     * @return Adapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Returns the path of the processed file.
     *
     * @return string
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Returns the strategyContainer in this command context.
     *
     * @return StrategyContainer
     */
    public function getStrategies()
    {
        return $this->strategies;
    }
}
