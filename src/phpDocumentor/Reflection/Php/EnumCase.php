<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Metadata\MetaDataContainer as MetaDataContainerInterface;

final class EnumCase implements Element, MetaDataContainerInterface
{
    use MetadataContainer;

    /** @var Fqsen */
    private $fqsen;

    /** @var DocBlock|null */
    private $docBlock;

    /** @var Location */
    private $location;

    /** @var Location */
    private $endLocation;

    /** @var string|null */
    private $value;

    public function __construct(
        Fqsen $fqsen,
        ?DocBlock $docBlock,
        ?Location $location = null,
        ?Location $endLocation = null,
        ?string $value = null
    ) {
        if ($location === null) {
            $location = new Location(-1);
        }

        if ($endLocation === null) {
            $endLocation = new Location(-1);
        }

        $this->fqsen    = $fqsen;
        $this->docBlock = $docBlock;
        $this->location = $location;
        $this->endLocation = $endLocation;
        $this->value = $value;
    }

    public function getFqsen(): Fqsen
    {
        return $this->fqsen;
    }

    public function getName(): string
    {
        return $this->fqsen->getName();
    }

    public function getDocBlock(): ?DocBlock
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

    public function getValue(): ?string
    {
        return $this->value;
    }
}
