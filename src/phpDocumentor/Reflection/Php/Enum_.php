<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Metadata\MetaDataContainer as MetaDataContainerInterface;
use phpDocumentor\Reflection\Type;

final class Enum_ implements Element, MetaDataContainerInterface
{
    use MetadataContainer;

    /** @var Fqsen Full Qualified Structural Element Name */
    private $fqsen;

    /** @var DocBlock|null */
    private $docBlock;

    /** @var Location */
    private $location;

    /** @var Location */
    private $endLocation;

    /** @var EnumCase[] */
    private $cases = [];

    /** @var array<string, Fqsen> */
    private $implements = [];

    /** @var array<string, Method> */
    private $methods = [];

    /** @var array<string, Fqsen> */
    private $usedTraits = [];

    /** @var Type|null */
    private $backedType;

    public function __construct(
        Fqsen $fqsen,
        ?Type $backedType,
        ?DocBlock $docBlock = null,
        ?Location $location = null,
        ?Location $endLocation = null
    ) {
        if ($location === null) {
            $location = new Location(-1);
        }

        if ($endLocation === null) {
            $endLocation = new Location(-1);
        }

        $this->fqsen       = $fqsen;
        $this->docBlock    = $docBlock;
        $this->location    = $location;
        $this->endLocation = $endLocation;
        $this->backedType  = $backedType;
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

    public function addCase(EnumCase $case): void
    {
        $this->cases[(string) $case->getFqsen()] = $case;
    }

    /** @return EnumCase[] */
    public function getCases(): array
    {
        return $this->cases;
    }

    /**
     * Returns the interfaces this enum is implementing.
     *
     * @return Fqsen[]
     */
    public function getInterfaces(): array
    {
        return $this->implements;
    }

    /**
     * Add an interface Fqsen this enum is implementing.
     */
    public function addInterface(Fqsen $interface): void
    {
        $this->implements[(string) $interface] = $interface;
    }

    /**
     * Returns the methods of this enum.
     *
     * @return Method[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Add a method to this enum.
     */
    public function addMethod(Method $method): void
    {
        $this->methods[(string) $method->getFqsen()] = $method;
    }

    /**
     * Returns the traits used by this enum.
     *
     * @return Fqsen[]
     */
    public function getUsedTraits(): array
    {
        return $this->usedTraits;
    }

    /**
     * Add trait fqsen used by this enum.
     */
    public function addUsedTrait(Fqsen $fqsen): void
    {
        $this->usedTraits[(string) $fqsen] = $fqsen;
    }

    public function getBackedType(): ?Type
    {
        return $this->backedType;
    }
}
