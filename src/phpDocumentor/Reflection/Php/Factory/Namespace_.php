<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory;

use InvalidArgumentException;
use Override;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\File as FileElement;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Types\NamespaceNodeToContext;
use PhpParser\Node\Stmt\Namespace_ as NamespaceNode;
use Webmozart\Assert\Assert;

use function get_debug_type;
use function sprintf;

class Namespace_ implements ProjectFactoryStrategy
{
    #[Override]
    public function matches(ContextStack $context, object $object): bool
    {
        return $object instanceof NamespaceNode;
    }

    /** @param NamespaceNode $object */
    #[Override]
    public function create(ContextStack $context, object $object, StrategyContainer $strategies): void
    {
        if (!$this->matches($context, $object)) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s cannot handle objects with the type %s',
                    self::class,
                    get_debug_type($object),
                ),
            );
        }

        $file = $context->peek();
        Assert::isInstanceOf($file, FileElement::class);
        $file->addNamespace($object->getAttribute('fqsen') ?? new Fqsen('\\'));
        $typeContext = (new NamespaceNodeToContext())($object);
        foreach ($object->stmts as $stmt) {
            $strategy = $strategies->findMatching($context, $stmt);
            $strategy->create($context->withTypeContext($typeContext), $stmt, $strategies);
        }
    }
}
