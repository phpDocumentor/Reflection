<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Metadata;

final class Hook implements Metadata
{
    private $hook;

    public function __construct(string $hook)
    {
        $this->hook = $hook;
    }

    public function key(): string
    {
        return "project-metadata";
    }

    public function hook(): string
    {
        return $this->hook;
    }
}
