<?php

namespace phpDocumentor\Reflection\Php\Factory;

use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Comment\Doc;
use PhpParser\Node;

abstract class AbstractFactory implements ProjectFactoryStrategy
{
    abstract public function matches($object);

    final public function create($object, StrategyContainer $strategies, Context $context = null)
    {
        if (!$this->matches($object)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s cannot handle objects with the type %s',
                    __CLASS__,
                    is_object($object) ? get_class($object) : gettype($object)
                )
            );
        }

        return $this->doCreate($object, $strategies, $context);
    }

    abstract protected function doCreate($object, StrategyContainer $strategies, Context $context = null);

    /**
     * @param Node|PropertyIterator|ClassConstantIterator|Doc $stmt
     * @param StrategyContainer $strategies
     * @param Context $context
     * @return Element
     */
    protected function createMember($stmt, StrategyContainer $strategies, Context $context = null)
    {
        $strategy = $strategies->findMatching($stmt);
        return $strategy->create($stmt, $strategies, $context);
    }

    /**
     * @param StrategyContainer $strategies
     * @param Doc $docBlock
     * @param Context $context
     * @return null|\phpDocumentor\Reflection\DocBlock
     */
    protected function createDocBlock(StrategyContainer $strategies, Doc $docBlock = null, Context $context = null)
    {
        if ($docBlock === null) {
            return null;
        }

        return $this->createMember($docBlock, $strategies, $context);
    }
}
