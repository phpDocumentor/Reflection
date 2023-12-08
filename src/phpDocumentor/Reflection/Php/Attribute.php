<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;

final class Attribute implements Element
{
    private Fqsen $fqsen;

    /** @var CallArgument[] */
    private array $arguments;

    /** @param CallArgument[] $arguments */
    public function __construct(Fqsen $fqsen, array $arguments)
    {
        $this->fqsen = $fqsen;
        $this->arguments = $arguments;
    }

    public function getFqsen(): Fqsen
    {
        return $this->fqsen;
    }

    /** @return CallArgument[] */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getName(): string
    {
        return $this->fqsen->getName();
    }
}
