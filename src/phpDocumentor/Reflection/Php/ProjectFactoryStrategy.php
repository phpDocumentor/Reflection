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

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Types\Context;

/**
 * Interface for strategies used by the project factory to build Elements out of nodes.
 */
interface ProjectFactoryStrategy
{
    /**
     * Returns true when the strategy is able to handle the object.
     *
     * @param mixed $object object to check.
     */
    public function matches($object) : bool;

    /**
     * Creates an Element out of the given object.
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param object            $object     object to convert to an Element
     * @param StrategyContainer $strategies used to convert nested objects.
     *
     * @return Element|DocBlock|File|Argument|null
     */
    public function create($object, StrategyContainer $strategies, ?Context $context = null);
}
