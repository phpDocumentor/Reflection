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


use phpDocumentor\Reflection\File;
use phpDocumentor\Reflection\Php\StrategyContainer;

/**
 * File Create command is used by the File Factory Strategy.
 * The command is passed to the registered middle ware classes.
 */
final class CreateCommand
{
    /**
     * @var File
     */
    private $file;

    /**
     * @var StrategyContainer
     */
    private $strategies;

    /**
     * Initializes this command.
     *
     * @param File $file
     * @param StrategyContainer $strategies
     */
    public function __construct(File $file, StrategyContainer $strategies)
    {
        $this->file = $file;
        $this->strategies = $strategies;
    }

    /**
     * Returns the path of the processed file.
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->file;
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

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }
}
