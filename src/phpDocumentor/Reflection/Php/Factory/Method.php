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
use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\Enum_;
use phpDocumentor\Reflection\Php\Interface_;
use phpDocumentor\Reflection\Php\Method as MethodDescriptor;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Php\Trait_;
use phpDocumentor\Reflection\Php\Visibility;
use PhpParser\Node\Stmt\ClassMethod;
use Webmozart\Assert\Assert;

use function is_array;

/**
 * Strategy to create MethodDescriptor and arguments when applicable.
 */
final class Method extends AbstractFactory implements ProjectFactoryStrategy
{
    public function matches(ContextStack $context, object $object): bool
    {
        return $object instanceof ClassMethod;
    }

    /**
     * Creates an MethodDescriptor out of the given object including its child elements.
     *
     * @param ClassMethod $object object to convert to an MethodDescriptor
     * @param ContextStack $context of the created object
     */
    protected function doCreate(
        ContextStack $context,
        object $object,
        StrategyContainer $strategies
    ): void {
        $methodContainer = $context->peek();
        Assert::isInstanceOfAny(
            $methodContainer,
            [
                Class_::class,
                Interface_::class,
                Trait_::class,
                Enum_::class,
            ]
        );

        $method = new MethodDescriptor(
            $object->getAttribute('fqsen'),
            $this->buildVisibility($object),
            $this->createDocBlock($object->getDocComment(), $context->getTypeContext()),
            $object->isAbstract(),
            $object->isStatic(),
            $object->isFinal(),
            new Location($object->getLine(), $object->getStartFilePos()),
            new Location($object->getEndLine(), $object->getEndFilePos()),
            (new Type())->fromPhpParser($object->getReturnType()),
            $object->byRef ?: false
        );
        $methodContainer->addMethod($method);

        $thisContext = $context->push($method);
        foreach ($object->params as $param) {
            $strategy = $strategies->findMatching($thisContext, $param);
            $strategy->create($thisContext, $param, $strategies);
        }

        if (!is_array($object->stmts)) {
            return;
        }

        foreach ($object->stmts as $stmt) {
            $strategy = $strategies->findMatching($thisContext, $stmt);
            $strategy->create($thisContext, $stmt, $strategies);
        }
    }

    /**
     * Converts the visibility of the method to a valid Visibility object.
     */
    private function buildVisibility(ClassMethod $node): Visibility
    {
        if ($node->isPrivate()) {
            return new Visibility(Visibility::PRIVATE_);
        }

        if ($node->isProtected()) {
            return new Visibility(Visibility::PROTECTED_);
        }

        return new Visibility(Visibility::PUBLIC_);
    }
}
