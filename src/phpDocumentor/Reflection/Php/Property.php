<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;

/**
 * Descriptor representing a property.
 */
final class Property implements Element
{
    /**
     * @var Fqsen
     */
    private $fqsen;

    /**
     * @var DocBlock|null
     */
    private $docBlock;

    /** @var string[] $types */
    private $types = [];

    /** @var null|string $default */
    private $default = null;

    /** @var bool $static */
    private $static = false;

    /** @var null|Visibility $visibility */
    private $visibility = null;

    /**
     * @var Location
     */
    private $location;

    /**
     * @param Visibility|null $visibility when null is provided a default 'public' is set.
     * @param DocBlock|null $docBlock
     * @param null|string $default
     * @param Location|null $location
     */
    public function __construct(
        Fqsen $fqsen,
        ?Visibility $visibility = null,
        ?DocBlock $docBlock = null,
        ?string $default = null,
        bool $static = false,
        ?Location $location = null
    ) {
        if ($location === null) {
            $location = new Location(-1);
        }

        $this->fqsen = $fqsen;
        $this->visibility = $visibility;
        $this->docBlock = $docBlock;
        $this->default = $default;
        $this->static = $static;
        $this->location = $location;

        if ($this->visibility === null) {
            $this->visibility = new Visibility('public');
        }
    }

    /**
     * returns the default value of this property.
     *
     * @return string
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
}
