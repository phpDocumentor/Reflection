<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\Metadata\Metadata;

final class MetadataStub implements Metadata
{
    public function __construct(private readonly string $key)
    {
    }

    public function key(): string
    {
        return $this->key;
    }
}
