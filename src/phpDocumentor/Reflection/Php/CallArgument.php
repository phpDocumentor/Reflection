<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php;

/**
 * Represents an argument in a function or method call.
 *
 * @api
 */
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
