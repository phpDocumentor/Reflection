<?php
declare(strict_types=1);

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

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Php\Trait_ as TraitElement;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property as PropertyNode;
use PhpParser\Node\Stmt\Trait_ as TraitNode;
use PhpParser\Node\Stmt\TraitUse;

// @codingStandardsIgnoreStart
final class Trait_ extends AbstractFactory implements ProjectFactoryStrategy
// @codingStandardsIgnoreEnd
{
    /**
     * Returns true when the strategy is able to handle the object.
     *
     *
     * @param mixed $object object to check.
     */
    public function matches($object): bool
    {
        return $object instanceof TraitNode;
    }

    /**
     * Creates an TraitElement out of the given object.
     *
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param TraitNode $object object to convert to an TraitElement
     * @param StrategyContainer $strategies used to convert nested objects.
     * @return TraitElement
     */
    protected function doCreate($object, StrategyContainer $strategies, Context $context = null)
    {
        $docBlock = $this->createDocBlock($strategies, $object->getDocComment(), $context);

        $trait = new TraitElement($object->fqsen, $docBlock, new Location($object->getLine()));

        if (isset($object->stmts)) {
            foreach ($object->stmts as $stmt) {
                switch (get_class($stmt)) {
                    case PropertyNode::class:
                        $properties = new PropertyIterator($stmt);
                        foreach ($properties as $property) {
                            $element = $this->createMember($property, $strategies, $context);
                            $trait->addProperty($element);
                        }
                        break;
                    case ClassMethod::class:
                        $method = $this->createMember($stmt, $strategies, $context);
                        $trait->addMethod($method);
                        break;
                    case TraitUse::class:
                        foreach ($stmt->traits as $use) {
                            $trait->addUsedTrait(new Fqsen('\\' . $use->toString()));
                        }
                        break;
                }
            }
        }

        return $trait;
    }
}
