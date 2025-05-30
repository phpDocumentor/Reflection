<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php;

use Override;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Metadata\MetaDataContainer as MetaDataContainerInterface;

/**
 * Represents a case in an Enum.
 *
 * @api
 */
final class EnumCase implements Element, MetaDataContainerInterface, AttributeContainer
{
    use MetadataContainer;
    use HasAttributes;

    private readonly Location $location;

    private readonly Location $endLocation;

    public function __construct(
        private readonly Fqsen $fqsen,
        private readonly DocBlock|null $docBlock,
        Location|null $location = null,
        Location|null $endLocation = null,
        private readonly string|null $value = null,
    ) {
        if ($location === null) {
            $location = new Location(-1);
        }

        if ($endLocation === null) {
            $endLocation = new Location(-1);
        }

        $this->location = $location;
        $this->endLocation = $endLocation;
    }

    #[Override]
    public function getFqsen(): Fqsen
    {
        return $this->fqsen;
    }

    #[Override]
    public function getName(): string
    {
        return $this->fqsen->getName();
    }

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

    public function getValue(): string|null
    {
        return $this->value;
    }
}
