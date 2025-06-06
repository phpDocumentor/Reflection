<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\NodeVisitor;

use PhpParser\NodeVisitor\FirstFindingVisitor as BaseFindingVisitor;

final class FindingVisitor extends BaseFindingVisitor
{
    public function __construct(callable $filterCallback)
    {
        parent::__construct($filterCallback);

        $this->foundNode = null;
    }
}
