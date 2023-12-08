<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php;

final class CallArgument
{
    public function __construct(private readonly string $value, private readonly string|null $name = null)
    {
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getName(): string|null
    {
        return $this->name;
    }
}
