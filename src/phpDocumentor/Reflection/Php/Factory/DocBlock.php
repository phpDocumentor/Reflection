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

use InvalidArgumentException;
use phpDocumentor\Reflection\DocBlock as DocBlockDescriptor;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Comment\Doc;
use function get_class;
use function gettype;
use function is_object;
use function sprintf;

/**
 * Strategy as wrapper around the DocBlockFactoryInterface.
 *
 * @see DocBlockFactoryInterface
 * @see DocBlockDescriptor
 */
final class DocBlock implements ProjectFactoryStrategy
{
    /**
     * Wrapped DocBlock factory
     *
     * @var DocBlockFactoryInterface
     */
    private $docblockFactory;

    /**
     * Initializes the object with a DocBlockFactory implementation.
     */
    public function __construct(DocBlockFactoryInterface $docBlockFactory)
    {
        $this->docblockFactory = $docBlockFactory;
    }

    /**
     * @param mixed $object
     */
    public function matches($object) : bool
    {
        return $object instanceof Doc;
    }

    /**
     * Creates an Element out of the given object.
     *
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param Doc $object object to convert to an Element
     * @param StrategyContainer $strategies used to convert nested objects.
     * @param Context $context of the created object
     *
     * @return DocBlockDescriptor|null
     */
    public function create($object, StrategyContainer $strategies, ?Context $context = null)
    {
        if ($object === null) {
            return null;
        }

        if (!$this->matches($object)) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s cannot handle objects with the type %s',
                    self::class,
                    is_object($object) ? get_class($object) : gettype($object)
                )
            );
        }

        return $this->docblockFactory->create($object->getText(), $context, new Location($object->getLine()));
    }
}
