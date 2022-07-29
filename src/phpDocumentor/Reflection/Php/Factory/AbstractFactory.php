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
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Comment\Doc;
use PhpParser\NodeAbstract;

use function get_class;
use function gettype;
use function is_object;
use function sprintf;

abstract class AbstractFactory implements ProjectFactoryStrategy
{
    private DocBlockFactoryInterface $docBlockFactory;

    public function __construct(DocBlockFactoryInterface $docBlockFactory)
    {
        $this->docBlockFactory = $docBlockFactory;
    }

    /**
     * Returns true when the strategy is able to handle the object.
     *
     * @param object $object object to check.
     */
    abstract public function matches(ContextStack $context, object $object): bool;

    public function create(ContextStack $context, object $object, StrategyContainer $strategies): void
    {
        if (!$this->matches($context, $object)) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s cannot handle objects with the type %s',
                    self::class,
                    is_object($object) ? get_class($object) : gettype($object)
                )
            );
        }

        $this->doCreate($context, $object, $strategies);
    }

    /**
     * Creates an Element out of the given object.
     *
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param NodeAbstract|object $object object to convert to an Element
     */
    abstract protected function doCreate(ContextStack $context, object $object, StrategyContainer $strategies): void;

    protected function createDocBlock(?Doc $docBlock = null, ?Context $context = null): ?DocBlock
    {
        if ($docBlock === null) {
            return null;
        }

        return $this->docBlockFactory->create($docBlock->getText(), $context);
    }
}
