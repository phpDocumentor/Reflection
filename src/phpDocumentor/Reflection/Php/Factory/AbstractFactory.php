<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory;

use InvalidArgumentException;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\Argument as ArgumentElement;
use phpDocumentor\Reflection\Php\File as PhpFile;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\NodeAbstract;
use function get_class;
use function gettype;
use function is_object;
use function sprintf;

abstract class AbstractFactory implements ProjectFactoryStrategy
{
    /**
     * Returns true when the strategy is able to handle the object.
     *
     * @param mixed $object object to check.
     */
    abstract public function matches($object) : bool;

    /**
     * @inheritDoc
     */
    public function create($object, StrategyContainer $strategies, ?Context $context = null)
    {
        if (!$this->matches($object)) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s cannot handle objects with the type %s',
                    self::class,
                    is_object($object) ? get_class($object) : gettype($object)
                )
            );
        }

        return $this->doCreate($object, $strategies, $context);
    }

    /**
     * Creates an Element out of the given object.
     *
     * Since an object might contain other objects that need to be converted the $factory is passed so it can be
     * used to create nested Elements.
     *
     * @param NodeAbstract|object $object object to convert to an Element
     * @param StrategyContainer $strategies used to convert nested objects.
     * @param Context $context of the created object
     *
     * @return DocBlock|Element|PhpFile|ArgumentElement
     */
    abstract protected function doCreate($object, StrategyContainer $strategies, ?Context $context = null);

    /**
     * @param Node|PropertyIterator|ClassConstantIterator|Doc $stmt
     *
     * @return mixed a child of Element
     */
    protected function createMember($stmt, StrategyContainer $strategies, ?Context $context = null)
    {
        $strategy = $strategies->findMatching($stmt);
        return $strategy->create($stmt, $strategies, $context);
    }

    protected function createDocBlock(
        ?StrategyContainer $strategies = null,
        ?Doc $docBlock = null,
        ?Context $context = null
    ) : ?DocBlock {
        if ($docBlock === null || $strategies === null) {
            return null;
        }

        return $this->createMember($docBlock, $strategies, $context);
    }
}
