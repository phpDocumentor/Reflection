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
use phpDocumentor\Reflection\Php\File as FileElement;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Php\Trait_ as TraitElement;
use PhpParser\Node\Stmt\Trait_ as TraitNode;
use Webmozart\Assert\Assert;

final class Trait_ extends AbstractFactory implements ProjectFactoryStrategy
{
    public function matches(ContextStack $context, object $object): bool
    {
        return $object instanceof TraitNode;
    }

    /**
     * Creates an TraitElement out of the given object.
     *
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param ContextStack $context used to convert nested objects.
     * @param TraitNode $object
     */
    protected function doCreate(ContextStack $context, object $object, StrategyContainer $strategies): void
    {
        $trait = new TraitElement(
            $object->fqsen,
            $this->createDocBlock($object->getDocComment(), $context->getTypeContext()),
            new Location($object->getLine()),
            new Location($object->getEndLine())
        );

        $file = $context->peek();
        Assert::isInstanceOf($file, FileElement::class);
        $file->addTrait($trait);

        if (!isset($object->stmts)) {
            return;
        }

        foreach ($object->stmts as $stmt) {
            $thisContext = $context->push($trait);
            $strategy = $strategies->findMatching($thisContext, $stmt);
            $strategy->create($thisContext, $stmt, $strategies);
        }
    }
}
