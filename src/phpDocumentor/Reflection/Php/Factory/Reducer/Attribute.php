<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory\Reducer;

use InvalidArgumentException;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\AttributeContainer;
use phpDocumentor\Reflection\Php\CallArgument;
use phpDocumentor\Reflection\Php\Factory\ContextStack;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\PrettyPrinter\Standard;

use function array_map;
use function assert;
use function get_class;
use function property_exists;
use function sprintf;

final class Attribute implements Reducer
{
    private Standard $printer;

    public function __construct()
    {
        $this->printer = new Standard();
    }

    public function reduce(
        ContextStack $context,
        object $object,
        StrategyContainer $strategies,
        ?object $carry
    ): ?object {
        if ($carry === null) {
            return null;
        }

        if (property_exists($object, 'attrGroups') === false || isset($object->attrGroups) === false) {
            return $carry;
        }

        if ($carry instanceof AttributeContainer === false) {
            throw new InvalidArgumentException(sprintf('Attribute can not be added on %s', get_class($carry)));
        }

        foreach ($object->attrGroups as $attrGroup) {
            assert($attrGroup instanceof Node\AttributeGroup);
            foreach ($attrGroup->attrs as $attr) {
                $carry->addAttribute(
                    new \phpDocumentor\Reflection\Php\Attribute(
                        new Fqsen('\\' . $attr->name->toString()),
                        array_map([$this, 'buildCallArgument'], $attr->args),
                    )
                );
            }
        }

        return $carry;
    }

    private function buildCallArgument(Arg $arg): CallArgument
    {
        return new CallArgument(
            $this->printer->prettyPrintExpr($arg->value),
            $arg->name !== null ? $arg->name->toString() : null,
        );
    }
}
