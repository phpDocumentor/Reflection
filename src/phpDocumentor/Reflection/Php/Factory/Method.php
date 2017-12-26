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

use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Method as MethodDescriptor;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Php\Visibility;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Mixed_;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\ClassMethod;

/**
 * Strategy to create MethodDescriptor and arguments when applicable.
 */
final class Method extends AbstractFactory implements ProjectFactoryStrategy
{
    /**
     * Returns true when the strategy is able to handle the object.
     *
     * @param object $object object to check.
     * @return boolean
     */
    public function matches($object)
    {
        return $object instanceof ClassMethod;
    }

    /**
     * Creates an MethodDescriptor out of the given object including its child elements.
     *
     * @param ClassMethod $object object to convert to an MethodDescriptor
     * @param StrategyContainer $strategies used to convert nested objects.
     * @param Context $context of the created object
     * @return MethodDescriptor
     */
    protected function doCreate($object, StrategyContainer $strategies, Context $context = null)
    {
        $docBlock = $this->createDocBlock($strategies, $object->getDocComment(), $context);

        $returnType = null;
        if ($object->getReturnType() !== null) {
            $typeResolver = new TypeResolver();
            if ($object->getReturnType() instanceof NullableType) {
                $typeString = '?' . $object->getReturnType()->type;
            } else {
                $typeString = (string)$object->getReturnType();
            }
            $returnType = $typeResolver->resolve($typeString, $context);
        }

        $method = new MethodDescriptor(
            $object->fqsen,
            $this->buildVisibility($object),
            $docBlock,
            $object->isAbstract(),
            $object->isStatic(),
            $object->isFinal(),
            new Location($object->getLine()),
            $returnType
        );

        foreach ($object->params as $param) {
            $method->addArgument($this->createMember($param, $strategies, $context));
        }

        return $method;
    }

    /**
     * Converts the visibility of the method to a valid Visibility object.
     *
     * @param ClassMethod $node
     * @return Visibility
     */
    private function buildVisibility(ClassMethod $node)
    {
        if ($node->isPrivate()) {
            return new Visibility(Visibility::PRIVATE_);
        } elseif ($node->isProtected()) {
            return new Visibility(Visibility::PROTECTED_);
        }

        return new Visibility(Visibility::PUBLIC_);
    }
}
