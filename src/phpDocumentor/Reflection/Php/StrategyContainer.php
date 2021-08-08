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

use phpDocumentor\Reflection\Exception;
use phpDocumentor\Reflection\Php\Factory\ContextStack;

/**
 * Interface for strategy containers.
 */
interface StrategyContainer
{
    /**
     * Find the ProjectFactoryStrategy that matches $object.
     *
     * @param mixed $object
     *
     * @throws Exception When no matching strategy was found.
     */
    public function findMatching(ContextStack $context, $object): ProjectFactoryStrategy;
}
