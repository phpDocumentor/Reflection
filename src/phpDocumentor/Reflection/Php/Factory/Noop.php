<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory;

use Override;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;

class Noop implements ProjectFactoryStrategy
{
    #[Override]
    public function matches(ContextStack $context, object $object): bool
    {
        return true;
    }

    #[Override]
    public function create(ContextStack $context, object $object, StrategyContainer $strategies): void
    {
    }
}
