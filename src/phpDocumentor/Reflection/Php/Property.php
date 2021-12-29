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

/**
 * Descriptor representing a property.
 */
final class Property implements Element, MetaDataContainerInterface
{
    use MetadataContainer;

    /** @var Fqsen */
    private $fqsen;

    /** @var DocBlock|null */
    private $docBlock;

    /** @var string[] $types */
    private $types = [];

    /** @var string|null $default */
    private $default = null;

    /** @var bool $static */
    private $static = false;

    /** @var Visibility|null $visibility */
    private $visibility = null;

    /** @var Location */
    private $location;

    /** @var Location */
    private $endLocation;

    /** @var Type|null */
    private $type;

    /** @var bool */
    private $readOnly;

    /**
     * @param Visibility|null $visibility when null is provided a default 'public' is set.
     */
    public function __construct(
        Fqsen $fqsen,
        ?Visibility $visibility = null,
        ?DocBlock $docBlock = null,
        ?string $default = null,
        bool $static = false,
        ?Location $location = null,
        ?Location $endLocation = null,
        ?Type $type = null,
        bool $readOnly = false
    ) {
        $this->fqsen = $fqsen;
        $this->visibility = $visibility ?: new Visibility('public');
        $this->docBlock = $docBlock;
        $this->default = $default;
        $this->static = $static;
        $this->location = $location ?: new Location(-1);
        $this->endLocation = $endLocation ?: new Location(-1);
        $this->type = $type;
        $this->readOnly = $readOnly;
    }

    /**
     * returns the default value of this property.
     */
    public function getDefault(): ?string
    {
        return $this->default;
    }

    /**
     * Returns true when this method is static. Otherwise returns false.
     */
    public function isStatic(): bool
    {
        return $this->static;
    }

    /**
     * Returns the types of this property.
     *
     * @return string[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * Add a type to this property
     */
    public function addType(string $type): void
    {
        $this->types[] = $type;
    }

    /**
     * Return visibility of the property.
     */
    public function getVisibility(): ?Visibility
    {
        return $this->visibility;
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

    /**
     * Returns the DocBlock of this property.
     */
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

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }
}
