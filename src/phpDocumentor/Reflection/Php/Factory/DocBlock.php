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
use phpDocumentor\Reflection\DocBlock as DocBlockDescriptor;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Comment\Doc;

/**
 * Strategy as wrapper around the DocBlockFactoryInterface.
 * @see DocBlockFactoryInterface
 * @see DocBlockDescriptor
 */
final class DocBlock implements ProjectFactoryStrategy
{
    /**
     * Wrapped DocBlock factory
     * @var DocBlockFactoryInterface
     */
    private $docblockFactory;

    /**
     * Initializes the object with a DocBlockFactory implementation.
     *
     * @param DocBlockFactoryInterface $docBlockFactory
     */
    public function __construct(DocBlockFactoryInterface $docBlockFactory)
    {
        $this->docblockFactory = $docBlockFactory;
    }

    /**
     * Returns true when the strategy is able to handle the object.
     *
     * @param Doc $object object to check.
     * @return boolean
     */
    public function matches($object)
    {
        return $object instanceof Doc;
    }

    /**
     * Creates an Element out of the given object.
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param Doc $object object to convert to an Element
     * @param StrategyContainer $strategies used to convert nested objects.
     * @return null|\phpDocumentor\Reflection\DocBlock
     */
    public function create($object, StrategyContainer $strategies)
    {
        if ($object === null) {
            return null;
        }

        if (!$this->matches($object)) {
            throw new InvalidArgumentException(
                sprintf('%s cannot handle objects with the type %s',
                    __CLASS__,
                    is_object($object) ? get_class($object) : gettype($object)
                )
            );
        }

        return $this->docblockFactory->create($object->getText());
    }
}
