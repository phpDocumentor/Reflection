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

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\File as FileElement;
use phpDocumentor\Reflection\Php\Interface_ as InterfaceElement;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Node\Stmt\Interface_ as InterfaceNode;
use Webmozart\Assert\Assert;

/**
 * Strategy to create a InterfaceElement including all sub elements.
 */
final class Interface_ extends AbstractFactory implements ProjectFactoryStrategy
{
    public function matches(ContextStack $context, object $object): bool
    {
        return $object instanceof InterfaceNode;
    }

    /**
     * Creates an Interface_ out of the given object.
     *
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param ContextStack      $context    of the created object
     * @param InterfaceNode     $object     object to convert to an Element
     * @param StrategyContainer $strategies used to convert nested objects.
     */
    protected function doCreate(
        ContextStack $context,
        object $object,
        StrategyContainer $strategies
    ): void {
        $docBlock = $this->createDocBlock($object->getDocComment(), $context->getTypeContext());
        $parents  = [];
        foreach ($object->extends as $extend) {
            $parents['\\' . (string) $extend] = new Fqsen('\\' . (string) $extend);
        }

        $interface = new InterfaceElement(
            $object->getAttribute('fqsen'),
            $parents,
            $docBlock,
            new Location($object->getLine()),
            new Location($object->getEndLine())
        );
        $file = $context->peek();
        Assert::isInstanceOf($file, FileElement::class);
        $file->addInterface($interface);

        foreach ($object->stmts as $stmt) {
            $thisContext = $context->push($interface);
            $strategy = $strategies->findMatching($thisContext, $stmt);
            $strategy->create($thisContext, $stmt, $strategies);
        }
    }
}
