<?php

declare(strict_types=1);

namespace PHP83;

class TypedConstants
{
    const string NAME = 'typed';
    const int COUNT = 42;
    const string|int UNION = 'either';
    const ?string NULLABLE = null;
    const UNTYPED = 'no type';
}
