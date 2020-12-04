<?php

declare(strict_types=1);

namespace PHP8;

use Foo\Date;

class UnionTypes
{
    private string|null|false $property;

    public function union(int|false $test): string|null|Date
    {

    }
}
