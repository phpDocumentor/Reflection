<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\Metadata\Metadata;

final class MetadataStub implements Metadata
{
    /** @var string */
    private $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function key(): string
    {
        return $this->key;
    }
}
