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

use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Factory;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;

/**
 * Strategy to convert Function_ to FunctionDescriptor
 *
 * @see \phpDocumentor\Descriptor\Funtion_
 * @see \PhpParser\Node\
 */
final class Function_ implements ProjectFactoryStrategy
{

    /**
     * Returns true when the strategy is able to handle the object.
     *
     * @param object $object object to check.
     * @return boolean
     */
    public function matches($object)
    {
        return $object instanceof \PhpParser\Node\Stmt\Function_;
    }

    /**
     * Creates an Element out of the given object.
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param object $object object to convert to an Element
     * @param StrategyContainer $strategies used to convert nested objects.
     * @return Element
     */
    public function create($object, StrategyContainer $strategies)
    {
        $function = new \phpDocumentor\Descriptor\Function_(new Fqsen($object->name));

        foreach ($object->params as $param) {
            $strategy = $strategies->findMatching($param);
            $function->addArgument($strategy->create($param, $strategies));
        }

        return $function;
    }
}