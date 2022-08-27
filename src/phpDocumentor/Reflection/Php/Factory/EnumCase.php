<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory;

use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Enum_ as EnumElement;
use phpDocumentor\Reflection\Php\EnumCase as EnumCaseElement;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Node\Stmt\EnumCase as EnumCaseNode;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;

use function assert;

final class EnumCase extends AbstractFactory
{
    private PrettyPrinter $prettyPrinter;

    public function __construct(DocBlockFactoryInterface $docBlockFactory, PrettyPrinter $prettyPrinter)
    {
        parent::__construct($docBlockFactory);
        $this->prettyPrinter = $prettyPrinter;
    }

    public function matches(ContextStack $context, object $object): bool
    {
        return $object instanceof EnumCaseNode;
    }

    /**
     * @param EnumCaseNode $object
     */
    protected function doCreate(ContextStack $context, object $object, StrategyContainer $strategies): void
    {
        $docBlock = $this->createDocBlock($object->getDocComment(), $context->getTypeContext());
        $enum = $context->peek();
        assert($enum instanceof EnumElement);
        $enum->addCase(new EnumCaseElement(
            $object->getAttribute('fqsen'),
            $docBlock,
            new Location($object->getLine()),
            new Location($object->getEndLine()),
            $object->expr !== null ? $this->prettyPrinter->prettyPrintExpr($object->expr) : null
        ));
    }
}
