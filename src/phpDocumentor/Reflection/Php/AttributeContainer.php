<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php;

interface AttributeContainer
{
    public function addAttribute(Attribute $attribute): void;

    /** @return Attribute[] */
    public function getAttributes(): array;
}
