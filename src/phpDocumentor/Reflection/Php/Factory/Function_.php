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

use Override;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\File as FileElement;
use phpDocumentor\Reflection\Php\Function_ as FunctionDescriptor;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Node\Stmt\Function_ as FunctionNode;
use Webmozart\Assert\Assert;

/**
 * Strategy to convert Function_ to FunctionDescriptor
 *
 * @see FunctionDescriptor
 * @see \PhpParser\Node\
 */
final class Function_ extends AbstractFactory implements ProjectFactoryStrategy
{
    #[Override]
    public function matches(ContextStack $context, object $object): bool
    {
        return $object instanceof FunctionNode && $context->peek() instanceof FileElement;
    }

    /**
     * Creates a FunctionDescriptor out of the given object including its child elements.
     *
     * @param ContextStack $context of the created object
     * @param FunctionNode $object
     */
    #[Override]
    protected function doCreate(
        ContextStack $context,
        object $object,
        StrategyContainer $strategies,
    ): object|null {
        $file = $context->peek();
        Assert::isInstanceOf($file, FileElement::class);

        $function = new FunctionDescriptor(
            $object->getAttribute('fqsen'),
            $this->createDocBlock($object->getDocComment(), $context->getTypeContext()),
            new Location($object->getLine()),
            new Location($object->getEndLine()),
            (new Type())->fromPhpParser($object->getReturnType()),
            $object->byRef ?: false,
        );

        $file->addFunction($function);

        $thisContext = $context->push($function);
        foreach ($object->stmts as $stmt) {
            $strategy = $strategies->findMatching($thisContext, $stmt);
            $strategy->create($thisContext, $stmt, $strategies);
        }

        return $function;
    }
}
