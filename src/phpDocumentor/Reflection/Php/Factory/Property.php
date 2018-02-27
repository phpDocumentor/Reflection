<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Factory;

use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\Property as PropertyDescriptor;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Php\Visibility;
use phpDocumentor\Reflection\PrettyPrinter;
use phpDocumentor\Reflection\Types\Context;

/**
 * Strategy to convert PropertyIterator to PropertyDescriptor
 *
 * @see PropertyDescriptor
 * @see PropertyIterator
 */
final class Property extends AbstractFactory implements ProjectFactoryStrategy
{
    /**
     * @var PrettyPrinter
     */
    private $valueConverter;

    /**
     * Initializes the object.
     */
    public function __construct(PrettyPrinter $prettyPrinter)
    {
        $this->valueConverter = $prettyPrinter;
    }

    /**
     * Returns true when the strategy is able to handle the object.
     *
     *
     * @param mixed $object object to check.
     */
    public function matches($object): bool
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
     * @return PropertyDescriptor
     */
    protected function doCreate($object, StrategyContainer $strategies, ?Context $context = null)
    {
        $visibility = $this->buildVisibility($object);
        $default = null;
        if ($object->getDefault() !== null) {
            $default = $this->valueConverter->prettyPrintExpr($object->getDefault());
        }

        $docBlock = $this->createDocBlock($strategies, $object->getDocComment(), $context);

        return new PropertyDescriptor(
            $object->getFqsen(),
            $visibility,
            $docBlock,
            $default,
            $object->isStatic(),
            new Location($object->getLine())
        );
    }

    /**
     * Converts the visibility of the property to a valid Visibility object.
     *
     * @return Visibility
     */
    private function buildVisibility(PropertyIterator $node)
    {
        if ($node->isPrivate()) {
            return new Visibility(Visibility::PRIVATE_);
        } elseif ($node->isProtected()) {
            return new Visibility(Visibility::PROTECTED_);
        }

        return new Visibility(Visibility::PUBLIC_);
    }
}
