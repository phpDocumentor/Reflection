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
use phpDocumentor\Descriptor\Class_ as ClassDescriptor;
use phpDocumentor\Descriptor\Method as MethodDescriptor;
use phpDocumentor\Descriptor\Property as PropertyDescriptor;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\PropertyProperty;

/**
 * Strategy to create a ClassDescriptor including all sub elements.
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
     * Creates an Element out of the given object.
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param ClassNode $object object to convert to an Element
     * @param StrategyContainer $strategies used to convert nested objects.
     * @return ClassDescriptor
     */
    public function create($object, StrategyContainer $strategies)
    {
        if (!$this->matches($object)) {
            throw new InvalidArgumentException(
                sprintf('%s cannot handle objects with the type %s',
                    __CLASS__,
                    is_object($object) ? get_class($object) : gettype($object)
                )
            );
        }

        $docBlock = null;

        $classDescriptor = new ClassDescriptor(
            new Fqsen('\\' . $object->name),
            $docBlock,
            $object->extends ? new Fqsen('\\' . $object->extends) : null,
            $object->isAbstract(),
            $object->isFinal()
        );

        if (isset($object->implements)) {
            foreach ($object->implements as $interfaceClassName) {
                $classDescriptor->addInterface(
                    new Fqsen('\\' . $interfaceClassName->toString())
                );
            }
        }

        if (isset($object->stmts)) {
            foreach ($object->stmts as $stmt) {
                switch (get_class($stmt)) {
                    case Property::class:
                        $properties = new PropertyHelper($stmt);
                        foreach ($properties as $property) {
                            $this->addCreateAndMember($property, $strategies, $classDescriptor);
                        }
                    default :
                        $this->addCreateAndMember($stmt, $strategies, $classDescriptor);
                        break;
                }
            }
        }

        return $classDescriptor;
    }

    private function addCreateAndMember($stmt, StrategyContainer $strategies, ClassDescriptor $classDescriptor)
    {
        $strategy = $strategies->findMatching($stmt);
        $descriptor = $strategy->create($stmt, $strategies);
        switch (get_class($descriptor)) {
            case MethodDescriptor::class:
                $classDescriptor->addMethod($descriptor);
                break;
            case PropertyDescriptor::class:
                $classDescriptor->addProperty($descriptor);
                break;
        }
    }
}