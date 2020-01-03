<?php

declare(strict_types=1);

/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Factory;

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Class_ as ClassModel;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;

/**
 * Stub for test purpose only.
 */
final class DummyFactoryStrategy implements ProjectFactoryStrategy
{
    /**
     * Returns true when the strategy is able to handle the object.
     *
     * @param mixed $object object to check.
     */
    public function matches($object) : bool
    {
        return true;
    }

    /**
     * Creates an Element out of the given object.
     *
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param mixed $object object to convert to an Element
     * @param StrategyContainer $strategies used to convert nested objects.
     *
     * @return mixed
     */
    public function create($object, StrategyContainer $strategies, ?Context $context = null)
    {
        return new ClassModel(new Fqsen('\Dummy'));
    }
}
