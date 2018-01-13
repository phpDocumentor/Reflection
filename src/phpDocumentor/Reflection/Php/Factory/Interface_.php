<?php
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

use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Interface_ as InterfaceElement;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Interface_ as InterfaceNode;

/**
 * Strategy to create a InterfaceElement including all sub elements.
 */
// @codingStandardsIgnoreStart
final class Interface_ extends AbstractFactory implements ProjectFactoryStrategy
// @codingStandardsIgnoreEnd
{
    /**
     * Returns true when the strategy is able to handle the object.
     *
     * @param InterfaceNode $object object to check.
     * @return boolean
     */
    public function matches($object)
    {
        return $object instanceof InterfaceNode;
    }

    /**
     * Creates an Interface_ out of the given object.
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param InterfaceNode $object object to convert to an Element
     * @param StrategyContainer $strategies used to convert nested objects.
     * @param Context $context of the created object
     * @return InterfaceElement
     */
    protected function doCreate($object, StrategyContainer $strategies, Context $context = null)
    {
        $docBlock = $this->createDocBlock($strategies, $object->getDocComment(), $context);
        $parents = [];
        foreach ($object->extends as $extend) {
            $parents['\\' . (string) $extend] = new Fqsen('\\' . (string) $extend);
        }

        $interface = new InterfaceElement($object->fqsen, $parents, $docBlock, new Location($object->getLine()));

        if (isset($object->stmts)) {
            foreach ($object->stmts as $stmt) {
                switch (get_class($stmt)) {
                    case ClassMethod::class:
                        $method = $this->createMember($stmt, $strategies, $context);
                        $interface->addMethod($method);
                        break;
                    case ClassConst::class:
                        $constants = new ClassConstantIterator($stmt);
                        foreach ($constants as $const) {
                            $element = $this->createMember($const, $strategies, $context);
                            $interface->addConstant($element);
                        }
                        break;
                }
            }
        }

        return $interface;
    }
}
