<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php;

interface AttributeContainer
{
    public function addAttribute(Attribute $attribute): void;
}
