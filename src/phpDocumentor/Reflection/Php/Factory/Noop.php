<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory;

use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;

class Noop implements ProjectFactoryStrategy
{
    public function matches(ContextStack $context, object $object): bool
    {
        return true;
    }

    public function create(ContextStack $context, object $object, StrategyContainer $strategies): void
    {
    }
}
