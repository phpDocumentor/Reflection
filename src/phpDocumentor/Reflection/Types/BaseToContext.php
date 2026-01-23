<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Types;

use PhpParser\Node;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;

use function array_filter;
use function array_map;
use function array_merge;
use function in_array;

/** @internal */
abstract class BaseToContext
{
    /**
     * @param GroupUse[]|Use_[] $usages
     *
     * @return array<string, string>
     */
    protected static function flattenUsage(array $usages): array
    {
        return array_merge([], ...array_merge([], ...array_map(
            static fn ($use): array => array_map(
                static function (Node\UseItem|UseUse $useUse) use ($use): array {
                    if ($use instanceof GroupUse) {
                        return [
                            (string) $useUse->getAlias() => $use->prefix->toString() . '\\' . $useUse->name->toString(),
                        ];
                    }

                    return [(string) $useUse->getAlias() => $useUse->name->toString()];
                },
                $use->uses,
            ),
            $usages,
        )));
    }

    /**
     * @param Node[] $nodes
     *
     * @return Use_[]|GroupUse[]
     */
    protected static function filterUsage(array $nodes): array
    {
        return array_filter(
            $nodes,
            static fn (Node $node): bool => (
                    $node instanceof Use_
                    || $node instanceof GroupUse
                ) && in_array($node->type, [Use_::TYPE_UNKNOWN, Use_::TYPE_NORMAL], true),
        );
    }
}
