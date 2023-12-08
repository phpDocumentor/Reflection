<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php;

final class CallArgument
{
    private string $value;

    private ?string $name;

    public function __construct(
        string $value,
        ?string $name = null
    ) {
        $this->value = $value;
        $this->name = $name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
