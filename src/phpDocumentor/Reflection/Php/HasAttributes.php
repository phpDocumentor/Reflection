<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php;

trait HasAttributes
{
    /** @var Attribute[] */
    private array $attributes = [];

    public function addAttribute(Attribute $attribute): void
    {
        $this->attributes[] = $attribute;
    }

    /** @return Attribute[] */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
