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
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Class_ as ClassElement;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property as PropertyNode;
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\TraitUse;

/**
 * Strategy to create a ClassElement including all sub elements.
 */
final class Class_ implements ProjectFactoryStrategy
{

    /**
     * Returns true when the strategy is able to handle the object.
     *
     * @param object $object object to check.
     * @return boolean
     */
    public function matches($object)
    {
        return $object instanceof ClassNode;
    }

    /**
     * Creates an ClassElement out of the given object.
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param ClassNode $object object to convert to an Element
     * @param StrategyContainer $strategies used to convert nested objects.
     * @param Context $context of the created object
     * @return ClassElement
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

        $docBlock = $this->createDocBlock($object->getDocComment(), $strategies, $context);

        $classElement = new ClassElement(
            $object->fqsen,
            $docBlock,
            $object->extends ? new Fqsen('\\' . $object->extends) : null,
            $object->isAbstract(),
            $object->isFinal()
        );

        if (isset($object->implements)) {
            foreach ($object->implements as $interfaceClassName) {
                $classElement->addInterface(
                    new Fqsen('\\' . $interfaceClassName->toString())
                );
            }
        }

        if (isset($object->stmts)) {
            foreach ($object->stmts as $stmt) {
                switch (get_class($stmt)) {
                    case TraitUse::class:
                        foreach ($stmt->traits as $use) {
                            $classElement->addUsedTrait(new Fqsen('\\'. $use->toString()));
                        }
                        break;
                    case PropertyNode::class:
                        $properties = new PropertyIterator($stmt);
                        foreach ($properties as $property) {
                            $element = $this->createMember($property, $strategies, $context);
                            $classElement->addProperty($element);
                        }
                        break;
                    case ClassMethod::class:
                        $method = $this->createMember($stmt, $strategies, $context);
                        $classElement->addMethod($method);
                        break;
                    case ClassConst::class:
                        $constants = new ClassConstantIterator($stmt);
                        foreach ($constants as $const) {
                            $element = $this->createMember($const, $strategies, $context);
                            $classElement->addConstant($element);
                        }
                        break;
                }
            }
        }

        return $classElement;
    }

    /**
     * @param Node|PropertyIterator|ClassConstantIterator $stmt
     * @param StrategyContainer $strategies
     * @param Context $context
     * @return Element
     */
    private function createMember($stmt, StrategyContainer $strategies, Context $context = null)
    {
        $strategy = $strategies->findMatching($stmt);
        return $strategy->create($stmt, $strategies, $context);
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

        return $this->createMember($docBlock, $strategies, $context);
    }
}
