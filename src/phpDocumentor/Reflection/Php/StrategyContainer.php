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

interface StrategyContainer
{
    /**
     * Find the ProjectFactoryStrategy that matches $object.
     *
     * @param mixed $object
     * @return ProjectFactoryStrategy
     * @throws Exception when no matching strategy was found.
     */
    public function findMatching($object);
}