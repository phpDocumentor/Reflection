<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php;

use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

trait NodeTrait {
    /** @var Stmt|null */
    protected $node;

    /** @var Expr|null */
    protected $defaultNode;

    /**
     * Returns the current PHP-Parser node that holds more detailed information
     * about the reflected object. e.g. position in the file and further attributes.
     * @return Stmt|null
     */
    public function getNode(): ?Stmt
    {
        return $this->node;
    }

    /**
     * @return Expr|null
     */
    public function getDefaultNode(): ?Expr
    {
        return $this->defaultNode;
    }
}
