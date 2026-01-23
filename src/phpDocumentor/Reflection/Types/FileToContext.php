<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Types;

use PhpParser\Node;

final class FileToContext extends BaseToContext
{
    /** @param Node[] $nodes */
    public function __invoke(array $nodes): Context
    {
        return new Context(
            '',
            self::flattenUsage(self::filterUsage($nodes)),
        );
    }
}
