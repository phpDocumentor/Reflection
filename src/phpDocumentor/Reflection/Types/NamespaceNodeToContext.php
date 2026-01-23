<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Types;

use PhpParser\Node\Stmt\Namespace_;

class NamespaceNodeToContext extends BaseToContext
{
    public function __invoke(Namespace_|null $namespace): Context
    {
        if (!$namespace) {
            return new Context('');
        }

        return new Context(
            $namespace->name ? $namespace->name->toString() : '',
            self::flattenUsage(self::filterUsage($namespace->stmts)),
        );
    }
}
