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
 * Descriptor representing a constant
 */
final class Constant implements Element, MetaDataContainerInterface
{
    use MetadataContainer;

    private Fqsen $fqsen;

    private ?DocBlock $docBlock;

    private ?string $value;

    private Location $location;

    private Location $endLocation;

    private Visibility $visibility;

    private bool $final;

    /**
     * Initializes the object.
     */
    public function __construct(
        Fqsen $fqsen,
        ?DocBlock $docBlock = null,
        ?string $value = null,
        ?Location $location = null,
        ?Location $endLocation = null,
        ?Visibility $visibility = null,
        bool $final = false
    ) {
        $this->fqsen = $fqsen;
        $this->docBlock = $docBlock;
        $this->value = $value;
        $this->location = $location ?: new Location(-1);
        $this->endLocation = $endLocation ?: new Location(-1);
        $this->visibility = $visibility ?: new Visibility(Visibility::PUBLIC_);
        $this->final = $final;
    }

    /**
     * Returns the value of this constant.
     */
    public function getValue(): ?string
    {
        return $this->value;
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
     * Returns DocBlock of this constant if available.
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

    public function getVisibility(): Visibility
    {
        return $this->visibility;
    }

    public function isFinal(): bool
    {
        return $this->final;
    }
}
