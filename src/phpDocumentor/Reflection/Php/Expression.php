<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;

final class Expression
{
    private string $expression;

    /** @var array<string, Fqsen|Type> */
    private array $parts;

    public function __construct(string $expression, array $parts)
    {
        $this->expression = $expression;
        $this->parts = $parts;
    }

    public function getExpression(): string
    {
        return $this->expression;
    }

    public function getParts(): array
    {
        return $this->parts;
    }

    public function __toString(): string
    {
        $valuesAsStrings = array_map(
            static fn(object $part): string => (string)$part,
            $this->parts
        );

        return str_replace(array_keys($this->parts), $valuesAsStrings, $this->expression);
    }
}
