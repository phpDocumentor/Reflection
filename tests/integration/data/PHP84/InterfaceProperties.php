<?php

declare(strict_types=1);

namespace PHP84;

interface HasId
{
    public int $id { get; }
}

interface HasName
{
    public string $name { get; set; }
}
