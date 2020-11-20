<?php

declare(strict_types=1);

namespace PHP8;

class MixedType
{
    public mixed $property;

    public function getProperty(): mixed
    {
        return $this->property;
    }

    public function setProperty(mixed $value): void
    {
        $this->property = $value;
    }
}
