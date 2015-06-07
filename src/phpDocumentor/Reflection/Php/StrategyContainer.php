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

use phpDocumentor\Reflection\Exception;

/**
 * Interface for strategy containers.
 */
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
