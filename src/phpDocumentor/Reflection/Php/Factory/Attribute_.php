<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory;

use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;

final class Attribute_ implements ProjectFactoryStrategy
{
    public function matches(ContextStack $context, object $object): bool
    {

    }

    public function create(ContextStack $context, object $object, StrategyContainer $strategies): void
    {
        // TODO: Implement create() method.
    }
}
