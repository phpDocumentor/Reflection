<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory;

use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\If_;

class IfStatement implements ProjectFactoryStrategy
{
    public function matches(ContextStack $context, object $object): bool
    {
        return $object instanceof If_;
    }

    /**
     * @param If_ $object
     */
    public function create(ContextStack $context, object $object, StrategyContainer $strategies): void
    {
        foreach ($object->stmts as $stmt) {
            $strategies->findMatching($context, $stmt)->create($context, $stmt, $strategies);
        }

        foreach ($object->elseifs as $elseIf) {
            foreach ($elseIf->stmts as $stmt) {
                $strategies->findMatching($context, $stmt)->create($context, $stmt, $strategies);
            }
        }

        if (!($object->else instanceof Else_)) {
            return;
        }

        foreach ($object->else->stmts as $stmt) {
            $strategies->findMatching($context, $stmt)->create($context, $stmt, $strategies);
        }
    }
}
