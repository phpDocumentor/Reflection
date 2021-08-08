<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory;

use InvalidArgumentException;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Php\Trait_;
use PhpParser\Node\Stmt\TraitUse as TraitUseNode;

final class TraitUse implements ProjectFactoryStrategy
{
    public function matches(ContextStack $context, object $object): bool
    {
        return $object instanceof TraitUseNode;
    }

    /**
     * @param ContextStack $context of the created object
     * @param TraitUseNode $object
     */
    public function create(ContextStack $context, object $object, StrategyContainer $strategies): void
    {
        if ($this->matches($context, $object) === false) {
            throw new InvalidArgumentException('Does not match expected node');
        }

        $class = $context->peek();

        if ($class instanceof Class_ === false && $class instanceof Trait_ === false) {
            throw new InvalidArgumentException('Traits can only be used in class or trait');
        }

        foreach ($object->traits as $trait) {
            $class->addUsedTrait(new Fqsen($trait->toCodeString()));
        }
    }
}
