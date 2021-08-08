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

use phpDocumentor\Reflection\Php\Factory\ContextStack;

/**
 * Interface for strategies used by the project factory to build Elements out of nodes.
 */
interface ProjectFactoryStrategy
{
    /**
     * Returns true when the strategy is able to handle the object.
     */
    public function matches(ContextStack $context, object $object): bool;

    /**
     * Creates an Element out of the given object.
     *
     * Since an object might contain other objects that need to be converted the $stategies are passed so it can be
     * used to create nested Elements. The passed ContextStack contains a stack of upstream created Elements that can
     * be manipulated by factories. This allows the factory to also impact on parent objects of earlier
     * created elements.
     *
     * @param Factory\ContextStack $context context to set the factory result.
     * @param object $object object to convert to an Element
     * @param StrategyContainer $strategies used to convert nested objects.
     */
    public function create(ContextStack $context, object $object, StrategyContainer $strategies): void;
}
