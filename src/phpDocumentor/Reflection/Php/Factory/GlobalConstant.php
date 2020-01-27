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

namespace phpDocumentor\Reflection\Php\Factory;

use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Constant as ConstantElement;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;

/**
 * Strategy to convert GlobalConstantIterator to ConstantElement
 *
 * @see ConstantElement
 * @see GlobalConstantIterator
 */
final class GlobalConstant extends AbstractFactory
{
    /** @var PrettyPrinter */
    private $valueConverter;

    /**
     * Initializes the object.
     */
    public function __construct(PrettyPrinter $prettyPrinter)
    {
        $this->valueConverter = $prettyPrinter;
    }

    public function matches($object) : bool
    {
        return $object instanceof GlobalConstantIterator;
    }

    /**
     * Creates an Constant out of the given object.
     *
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param GlobalConstantIterator $object object to convert to an Element
     * @param StrategyContainer $strategies used to convert nested objects.
     * @param Context $context of the created object
     *
     * @return ConstantElement
     */
    protected function doCreate($object, StrategyContainer $strategies, ?Context $context = null)
    {
        return new ConstantElement(
            $object->getFqsen(),
            $this->createDocBlock($strategies, $object->getDocComment(), $context),
            $object->getValue() !== null ? $this->valueConverter->prettyPrintExpr($object->getValue()) : null,
            new Location($object->getLine())
        );
    }
}
