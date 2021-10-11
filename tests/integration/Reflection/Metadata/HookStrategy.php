<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Metadata;

use phpDocumentor\Reflection\Php\Factory\ContextStack;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\Expression;

final class HookStrategy implements ProjectFactoryStrategy
{
    public function matches(ContextStack $context, object $object): bool
    {
        if ($object instanceof Expression === false) {
            return false;
        }

        return $object->expr instanceof FuncCall && ((string)$object->expr->name) === 'hook';
    }

    public function create(ContextStack $context, object $object, StrategyContainer $strategies): void
    {
        $method = $context->peek();
        $method->addMetadata(new Hook($object->expr->args[0]->value->value));
    }
}
