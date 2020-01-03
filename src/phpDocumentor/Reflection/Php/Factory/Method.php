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
use phpDocumentor\Reflection\Php\Method as MethodDescriptor;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Php\Visibility;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\UnionType;

/**
 * Strategy to create MethodDescriptor and arguments when applicable.
 */
final class Method extends AbstractFactory implements ProjectFactoryStrategy
{
    public function matches($object) : bool
    {
        return $object instanceof ClassMethod;
    }

    /**
     * Creates an MethodDescriptor out of the given object including its child elements.
     *
     * @param ClassMethod $object object to convert to an MethodDescriptor
     * @param StrategyContainer $strategies used to convert nested objects.
     * @param Context $context of the created object
     *
     * @return MethodDescriptor
     */
    protected function doCreate($object, StrategyContainer $strategies, ?Context $context = null)
    {
        $docBlock = $this->createDocBlock($strategies, $object->getDocComment(), $context);

        $returnType = null;
        if ($object->getReturnType() !== null) {
            $typeResolver = new TypeResolver();
            if ($object->getReturnType() instanceof NullableType) {
                $typeString = '?' . $object->getReturnType()->type;
            } elseif ($object->getReturnType() instanceof UnionType) {
                $typeString = $object->getReturnType()->getType();
            } else {
                $typeString = $object->getReturnType()->toString();
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
     */
    private function buildVisibility(ClassMethod $node) : Visibility
    {
        if ($node->isPrivate()) {
            return new Visibility(Visibility::PRIVATE_);
        } elseif ($node->isProtected()) {
            return new Visibility(Visibility::PROTECTED_);
        }

        return new Visibility(Visibility::PUBLIC_);
    }
}
