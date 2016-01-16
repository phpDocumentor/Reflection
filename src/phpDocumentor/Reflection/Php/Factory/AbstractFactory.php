<?php

namespace phpDocumentor\Reflection\Php\Factory;

use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Middleware\ChainFactory;
use phpDocumentor\Reflection\Middleware\Middleware;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Comment\Doc;
use PhpParser\Node;

abstract class AbstractFactory implements ProjectFactoryStrategy
{
    /**
     * @var callable
     */
    private $middlewareChain;

    public function __construct($middleware = [])
    {
        $lastCallable = function(CreateCommand $command) {
            return $this->doCreate($command->getObject(), $command->getStrategies(), $command->getContext());
        };

        $this->middlewareChain = ChainFactory::createExecutionChain($middleware, $lastCallable);
    }


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

        $command = new CreateCommand($object, $strategies, $context);
        $middleware = $this->middlewareChain;

        return $middleware($command);
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
