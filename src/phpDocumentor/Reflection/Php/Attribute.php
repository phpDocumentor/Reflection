<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php;

use Override;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;

/** @api */
final class Attribute implements Element
{
    /** @param CallArgument[] $arguments */
    public function __construct(private readonly Fqsen $fqsen, private readonly array $arguments)
    {
    }

    #[Override]
    public function getFqsen(): Fqsen
    {
        return $this->fqsen;
    }

    /** @return CallArgument[] */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    #[Override]
    public function getName(): string
    {
        return $this->fqsen->getName();
    }
}
