<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory\Reducer;

use InvalidArgumentException;
use Override;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\AttributeContainer;
use phpDocumentor\Reflection\Php\CallArgument;
use phpDocumentor\Reflection\Php\Factory\ContextStack;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Node\Arg;
use PhpParser\Node\AttributeGroup;
use PhpParser\PrettyPrinter\Standard;

use function array_map;
use function assert;
use function property_exists;
use function sprintf;

final class Attribute implements Reducer
{
    private readonly Standard $printer;

    public function __construct()
    {
        $this->printer = new Standard();
    }

    #[Override]
    public function reduce(
        ContextStack $context,
        object $object,
        StrategyContainer $strategies,
        object|null $carry,
    ): object|null {
        if ($carry === null) {
            return null;
        }

        if (property_exists($object, 'attrGroups') === false || isset($object->attrGroups) === false) {
            return $carry;
        }

        if ($carry instanceof AttributeContainer === false) {
            throw new InvalidArgumentException(sprintf('Attribute can not be added on %s', $carry::class));
        }

        foreach ($object->attrGroups as $attrGroup) {
            assert($attrGroup instanceof AttributeGroup);
            foreach ($attrGroup->attrs as $attr) {
                $carry->addAttribute(
                    new \phpDocumentor\Reflection\Php\Attribute(
                        new Fqsen('\\' . $attr->name->toString()),
                        array_map($this->buildCallArgument(...), $attr->args),
                    ),
                );
            }
        }

        return $carry;
    }

    private function buildCallArgument(Arg $arg): CallArgument
    {
        return new CallArgument(
            $this->printer->prettyPrintExpr($arg->value),
            $arg->name?->toString(),
        );
    }
}
