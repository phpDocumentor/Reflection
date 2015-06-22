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

use InvalidArgumentException;
use phpDocumentor\Reflection\Php\Argument as ArgumentDescriptor;
use phpDocumentor\Reflection\Php\Factory;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Node\Param;

/**
 * Strategy to convert Param to Argument
 *
 * @see \phpDocumentor\Descriptor\Argument
 * @see \PhpParser\Node\Arg
 */
final class Argument implements ProjectFactoryStrategy
{

    /**
     * Returns true when the strategy is able to handle the object.
     *
     * @param Param $object object to check.
     * @return boolean
     */
    public function matches($object)
    {
        return $object instanceof Param;
    }

    /**
     * Creates an ArgumentDescriptor out of the given object.
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param Param $object object to convert to an Element
     * @param StrategyContainer $strategies used to convert nested objects.
     * @return ArgumentDescriptor
     *
     * @throws InvalidArgumentException when this strategy is not able to handle $object
     */
    public function create($object, StrategyContainer $strategies)
    {
        if (!$this->matches($object)) {
            throw new InvalidArgumentException(
                sprintf('%s cannot handle objects with the type %s',
                    __CLASS__,
                    is_object($object) ? get_class($object) : gettype($object)
                )
            );
        }

        return new ArgumentDescriptor($object->name, $object->default, $object->byRef, $object->variadic);
    }
}
