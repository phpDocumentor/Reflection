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

namespace phpDocumentor\Reflection\Php\Factory;

use InvalidArgumentException;
use phpDocumentor\Reflection\Php\Factory;
use phpDocumentor\Reflection\Php\Property as PropertyDescriptor;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Php\Visibility;
use phpDocumentor\Reflection\PrettyPrinter;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Comment\Doc;

/**
 * Strategy to convert PropertyIterator to PropertyDescriptor
 *
 * @see PropertyDescriptor
 * @see PropertyIterator
 */
final class Property implements ProjectFactoryStrategy
{
    /**
     * @var PrettyPrinter
     */
    private $valueConverter;

    /**
     * Initializes the object.
     *
     * @param PrettyPrinter $prettyPrinter
     */
    public function __construct(PrettyPrinter $prettyPrinter)
    {
        $this->valueConverter = $prettyPrinter;
    }

    /**
     * Returns true when the strategy is able to handle the object.
     *
     * @param object $object object to check.
     * @return boolean
     */
    public function matches($object)
    {
        return $object instanceof PropertyIterator;
    }

    /**
     * Creates an PropertyDescriptor out of the given object.
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param PropertyIterator $object object to convert to an PropertyDescriptor
     * @param StrategyContainer $strategies used to convert nested objects.
     * @param Context $context
     * @return PropertyDescriptor
     */
    public function create($object, StrategyContainer $strategies, Context $context = null)
    {
        if (!$this->matches($object)) {
            throw new InvalidArgumentException(
                sprintf('%s cannot handle objects with the type %s',
                    __CLASS__,
                    is_object($object) ? get_class($object) : gettype($object)
                )
            );
        }

        $visibility = $this->buildVisibility($object);
        $default = null;
        if ($object->getDefault() !== null) {
            $default = $this->valueConverter->prettyPrintExpr($object->getDefault());
        }
        $docBlock = $this->createDocBlock($object->getDocComment(), $strategies, $context);

        return new PropertyDescriptor($object->getFqsen(), $visibility, $docBlock, $default, $object->isStatic());
    }

    /**
     * Converts the visibility of the property to a valid Visibility object.
     *
     * @param PropertyIterator $node
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

    /**
     * @param Doc $docBlock
     * @param StrategyContainer $strategies
     * @param Context $context
     * @return null|\phpDocumentor\Reflection\DocBlock
     */
    private function createDocBlock(Doc $docBlock = null, StrategyContainer $strategies, Context $context = null)
    {
        if ($docBlock === null) {
            return null;
        }

        $strategy = $strategies->findMatching($docBlock);
        return $strategy->create($docBlock, $strategies, $context);
    }
}
