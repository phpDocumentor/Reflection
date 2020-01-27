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
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\Property as PropertyDescriptor;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Php\Visibility;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;

/**
 * Strategy to convert PropertyIterator to PropertyDescriptor
 *
 * @see PropertyDescriptor
 * @see PropertyIterator
 */
final class Property extends AbstractFactory implements ProjectFactoryStrategy
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
        return $object instanceof PropertyIterator;
    }

    /**
     * Creates an PropertyDescriptor out of the given object.
     *
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param PropertyIterator $object object to convert to an PropertyDescriptor
     * @param StrategyContainer $strategies used to convert nested objects.
     *
     * @return PropertyDescriptor
     */
    protected function doCreate($object, StrategyContainer $strategies, ?Context $context = null)
    {
        $default = null;
        if ($object->getDefault() !== null) {
            $default = $this->valueConverter->prettyPrintExpr($object->getDefault());
        }

        return new PropertyDescriptor(
            $object->getFqsen(),
            $this->buildVisibility($object),
            $this->createDocBlock($strategies, $object->getDocComment(), $context),
            $default,
            $object->isStatic(),
            new Location($object->getLine()),
            (new Type())->fromPhpParser($object->getType())
        );
    }

    /**
     * Converts the visibility of the property to a valid Visibility object.
     */
    private function buildVisibility(PropertyIterator $node) : Visibility
    {
        if ($node->isPrivate()) {
            return new Visibility(Visibility::PRIVATE_);
        } elseif ($node->isProtected()) {
            return new Visibility(Visibility::PROTECTED_);
        }

        return new Visibility(Visibility::PUBLIC_);
    }
}
