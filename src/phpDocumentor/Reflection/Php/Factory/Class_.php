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
use phpDocumentor\Reflection\Php\Class_ as ClassElement;
use phpDocumentor\Reflection\Php\File as FileElement;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Node\Stmt\Class_ as ClassNode;

use function assert;

/**
 * Strategy to create a ClassElement including all sub elements.
 */
final class Class_ extends AbstractFactory implements ProjectFactoryStrategy
{
    public function matches(ContextStack $context, object $object): bool
    {
        return $object instanceof ClassNode;
    }

    /**
     * Creates an ClassElement out of the given object.
     *
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param ContextStack $context of the created object
     * @param ClassNode $object
     */
    protected function doCreate(ContextStack $context, object $object, StrategyContainer $strategies): void
    {
        $docBlock = $this->createDocBlock($object->getDocComment(), $context->getTypeContext());

        $classElement = new ClassElement(
            $object->fqsen,
            $docBlock,
            $object->extends ? new Fqsen('\\' . $object->extends) : null,
            $object->isAbstract(),
            $object->isFinal(),
            new Location($object->getLine()),
            new Location($object->getEndLine())
        );

        if (isset($object->implements)) {
            foreach ($object->implements as $interfaceClassName) {
                $classElement->addInterface(
                    new Fqsen('\\' . $interfaceClassName->toString())
                );
            }
        }

        $file = $context->peek();
        assert($file instanceof FileElement);
        $file->addClass($classElement);

        if (!isset($object->stmts)) {
            return;
        }

        foreach ($object->stmts as $stmt) {
            $thisContext = $context->push($classElement);
            $strategy = $strategies->findMatching($thisContext, $stmt);
            $strategy->create($thisContext, $stmt, $strategies);
        }
    }
}
