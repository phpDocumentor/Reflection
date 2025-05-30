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

use Override;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Metadata\MetaDataContainer as MetaDataContainerInterface;
use phpDocumentor\Reflection\Type;

/**
 * Descriptor representing an Enum.
 *
 * @api
 */
final class Enum_ implements Element, MetaDataContainerInterface, AttributeContainer
{
    use MetadataContainer;
    use HasAttributes;

    private readonly Location $location;

    private readonly Location $endLocation;

    /** @var EnumCase[] */
    private array $cases = [];

    /** @var array<string, Fqsen> */
    private array $implements = [];

    /** @var Constant[] References to constants defined in this enum. */
    private array $constants = [];

    /** @var array<string, Method> */
    private array $methods = [];

    /** @var array<string, Fqsen> */
    private array $usedTraits = [];

    public function __construct(
        /** @var Fqsen Full Qualified Structural Element Name */
        private readonly Fqsen $fqsen,
        private readonly Type|null $backedType,
        private readonly DocBlock|null $docBlock = null,
        Location|null $location = null,
        Location|null $endLocation = null,
    ) {
        if ($location === null) {
            $location = new Location(-1);
        }

        if ($endLocation === null) {
            $endLocation = new Location(-1);
        }

        $this->location    = $location;
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
     * Returns the constants of this enum.
     *
     * @return Constant[]
     */
    public function getConstants(): array
    {
        return $this->constants;
    }

    /**
     * Add Constant to this enum.
     */
    public function addConstant(Constant $constant): void
    {
        $this->constants[(string) $constant->getFqsen()] = $constant;
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

    public function getBackedType(): Type|null
    {
        return $this->backedType;
    }
}
