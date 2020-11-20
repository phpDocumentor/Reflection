<?php

declare(strict_types=1);

namespace PHP8;

class StaticType
{
    //Static is not allowed here
    public static $property;

    public function getProperty(): static
    {
        return $this->property;
    }

    public function setProperty($value): void
    {
        $this->property = $value;
    }
}
