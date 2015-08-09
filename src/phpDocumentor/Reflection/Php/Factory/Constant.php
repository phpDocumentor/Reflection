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

use phpDocumentor\Reflection\Php\Constant as ConstantElement;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\PrettyPrinter;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Comment\Doc;

/**
 * Strategy to convert ClassConstantIterator to ConstantElement
 *
 * @see ConstantElement
 * @see ClassConstantIterator
 */
class Constant implements ProjectFactoryStrategy
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
        return $object instanceof ClassConstantIterator;
    }

    /**
     * Creates an Constant out of the given object.
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param ClassConstantIterator $object object to convert to an Element
     * @param StrategyContainer $strategies used to convert nested objects.
     * @param Context $context of the created object
     * @return Constant
     */
    public function create($object, StrategyContainer $strategies, Context $context = null)
    {
        $docBlock = $this->createDocBlock($object->getDocComment(), $strategies, $context);
        $default = null;
        if ($object->getValue() !== null) {
            $default = $this->valueConverter->prettyPrintExpr($object->getValue());
        }

        return new ConstantElement($object->getFqsen(), $docBlock, $default);
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
