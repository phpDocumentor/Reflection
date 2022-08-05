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

/**
 * Descriptor representing a Class.
 */
// @codingStandardsIgnoreStart
final class Class_ implements Element, MetaDataContainerInterface
// @codingStandardsIgnoreEnd
{
    use MetadataContainer;

    /** @var Fqsen Full Qualified Structural Element Name */
    private Fqsen $fqsen;

    private ?DocBlock $docBlock = null;

    private bool $readOnly = false;

    /** @var bool Whether this is an abstract class. */
    private bool $abstract = false;

    /** @var bool Whether this class is marked as final and can't be subclassed. */
    private bool $final = false;

    /** @var Fqsen|null The class this class is extending. */
    private ?Fqsen $parent = null;

    /** @var Fqsen[] References to interfaces that are implemented by this class. */
    private array $implements = [];

    /** @var Constant[] References to constants defined in this class. */
    private array $constants = [];

    /** @var Property[] References to properties defined in this class. */
    private array $properties = [];

    /** @var Method[] References to methods defined in this class. */
    private array $methods = [];

    /** @var Fqsen[] References to traits consumed by this class */
    private array $usedTraits = [];

    private Location $location;

    private Location $endLocation;

    /**
     * Initializes a number of properties with the given values. Others are initialized by definition.
     */
    public function __construct(
        Fqsen $fqsen,
        ?DocBlock $docBlock = null,
        ?Fqsen $parent = null,
        bool $abstract = false,
        bool $final = false,
        ?Location $location = null,
        ?Location $endLocation = null,
        bool $readOnly = false
    ) {
        if ($location === null) {
            $location = new Location(-1);
        }

        if ($endLocation === null) {
            $endLocation = new Location(-1);
        }

        $this->fqsen       = $fqsen;
        $this->parent      = $parent;
        $this->docBlock    = $docBlock;
        $this->abstract    = $abstract;
        $this->final       = $final;
        $this->location    = $location;
        $this->endLocation = $endLocation;
        $this->readOnly = $readOnly;
    }

    /**
     * Returns true when this class is final. Otherwise returns false.
     */
    public function isFinal(): bool
    {
        return $this->final;
    }

    /**
     * Returns true when this class is abstract. Otherwise returns false.
     */
    public function isAbstract(): bool
    {
        return $this->abstract;
    }

    /**
     * Returns true when this class is read-only. Otherwise returns false.
     */
    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    /**
     * Returns the superclass this class is extending if available.
     */
    public function getParent(): ?Fqsen
    {
        return $this->parent;
    }

    /**
     * Returns the interfaces this class is implementing.
     *
     * @return Fqsen[]
     */
    public function getInterfaces(): array
    {
        return $this->implements;
    }

    /**
     * Add a interface Fqsen this class is implementing.
     */
    public function addInterface(Fqsen $interface): void
    {
        $this->implements[(string) $interface] = $interface;
    }

    /**
     * Returns the constants of this class.
     *
     * @return Constant[]
     */
    public function getConstants(): array
    {
        return $this->constants;
    }

    /**
     * Add Constant to this class.
     */
    public function addConstant(Constant $constant): void
    {
        $this->constants[(string) $constant->getFqsen()] = $constant;
    }

    /**
     * Returns the methods of this class.
     *
     * @return Method[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Add a method to this class.
     */
    public function addMethod(Method $method): void
    {
        $this->methods[(string) $method->getFqsen()] = $method;
    }

    /**
     * Returns the properties of this class.
     *
     * @return Property[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * Add a property to this class.
     */
    public function addProperty(Property $property): void
    {
        $this->properties[(string) $property->getFqsen()] = $property;
    }

    /**
     * Returns the traits used by this class.
     *
     * @return Fqsen[]
     */
    public function getUsedTraits(): array
    {
        return $this->usedTraits;
    }

    /**
     * Add trait fqsen used by this class.
     */
    public function addUsedTrait(Fqsen $fqsen): void
    {
        $this->usedTraits[(string) $fqsen] = $fqsen;
    }

    /**
     * Returns the Fqsen of the element.
     */
    public function getFqsen(): Fqsen
    {
        return $this->fqsen;
    }

    /**
     * Returns the name of the element.
     */
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
}
