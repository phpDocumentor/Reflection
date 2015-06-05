<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2015 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Factory;

use phpDocumentor\Descriptor\Argument as ArgumentDescriptor;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\Factory;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use PhpParser\Node\Param;

/**
 * Strategy to convert Param to Argument
 *
 * @see \phpDocumentor\Descriptor\Argument
 * @see \PhpParser\Node\Arg
 */
class Argument implements ProjectFactoryStrategy
{

    /**
     * Returns true when the strategy is able to handle the object.
     *
     * @param object $object object to check.
     * @return boolean
     */
    public function matches($object)
    {
        return $object instanceof Param;
    }

    /**
     * Creates an Element out of the given object.
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param object $object object to convert to an Element
     * @param Factory $factory used to convert nested objects.
     * @return Element
     */
    public function create($object, Factory $factory)
    {
        return new ArgumentDescriptor($object->name, $object->default, $object->byRef, $object->variadic);
    }
}
