<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Metadata\MetaDataContainer as MetaDataContainerInterface;

/** @api */
final class PropertyHook implements AttributeContainer, MetaDataContainerInterface
{
    use MetadataContainer;
    use HasAttributes;

    /** @var Argument[] */
    private array $arguments = [];

    private readonly Location $location;

    private readonly Location $endLocation;

    public function __construct(
        private readonly string $name,
        private readonly Visibility $visibility,
        private readonly DocBlock|null $docBlock = null,
        private readonly bool $final = false,
        Location|null $location = null,
        Location|null $endLocation = null,
    ) {
        $this->location = $location ?? new Location(-1);
        $this->endLocation = $endLocation ?? new Location(-1);
    }

    /**
     * Returns true when this hook is final. Otherwise, returns false.
     */
    public function isFinal(): bool
    {
        return $this->final;
    }

    /**
     * Returns the Visibility of this hook.
     */
    public function getVisibility(): Visibility|null
    {
        return $this->visibility;
    }

    /**
     * Returns the arguments of this hook.
     *
     * @return Argument[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Add new argument to this hook.
     */
    public function addArgument(Argument $argument): void
    {
        $this->arguments[] = $argument;
    }

    /**
     * Returns the name of this hook.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the DocBlock of this method if available.
     */
    public function getDocBlock(): DocBlock|null
    {
        return $this->docBlock;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getEndLocation(): Location
    {
        return $this->endLocation;
    }
}
