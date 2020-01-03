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

use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Function_ as FunctionDescriptor;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\Function_ as FunctionNode;

/**
 * Strategy to convert Function_ to FunctionDescriptor
 *
 * @see FunctionDescriptor
 * @see \PhpParser\Node\
 */
// @codingStandardsIgnoreStart
final class Function_ extends AbstractFactory implements ProjectFactoryStrategy
// @codingStandardsIgnoreEnd
{
    public function matches($object) : bool
    {
        return $object instanceof FunctionNode;
    }

    /**
     * Creates a FunctionDescriptor out of the given object including its child elements.
     *
     * @param \PhpParser\Node\Stmt\Function_ $object object to convert to an Element
     * @param StrategyContainer $strategies used to convert nested objects.
     * @param Context $context of the created object
     * @return FunctionDescriptor
     */
    protected function doCreate($object, StrategyContainer $strategies, ?Context $context = null)
    {
        $docBlock = $this->createDocBlock($strategies, $object->getDocComment(), $context);

        $returnType = null;
        if ($object->getReturnType() !== null) {
            $typeResolver = new TypeResolver();
            if ($object->getReturnType() instanceof NullableType) {
                $typeString = '?' . $object->getReturnType()->type;
            } else {
                $typeString = (string) $object->getReturnType();
            }

            $returnType = $typeResolver->resolve($typeString, $context);
        }

        $function = new FunctionDescriptor($object->fqsen, $docBlock, new Location($object->getLine()), $returnType);

        foreach ($object->params as $param) {
            $strategy = $strategies->findMatching($param);
            $function->addArgument($strategy->create($param, $strategies, $context));
        }

        return $function;
    }
}
