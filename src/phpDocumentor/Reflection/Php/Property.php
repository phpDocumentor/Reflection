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

use function is_string;
use function trigger_error;

use const E_USER_DEPRECATED;

/**
 * Descriptor representing a property.
 */
final class Property implements Element, MetaDataContainerInterface
{
    use MetadataContainer;

    private Fqsen $fqsen;

    private ?DocBlock $docBlock;

    /** @var string[] $types */
    private array $types = [];

    private ?Expression $default = null;

    private bool $static = false;

    private ?Visibility $visibility = null;

    private Location $location;

    private Location $endLocation;

    private ?Type $type;

    private bool $readOnly;

    /**
     * @param Visibility|null $visibility when null is provided a default 'public' is set.
     * @param string|Expression|null $default
     */
    public function __construct(
        Fqsen $fqsen,
        ?Visibility $visibility = null,
        ?DocBlock $docBlock = null,
        $default = null,
        bool $static = false,
        ?Location $location = null,
        ?Location $endLocation = null,
        ?Type $type = null,
        bool $readOnly = false
    ) {
        $this->fqsen = $fqsen;
        $this->visibility = $visibility ?: new Visibility('public');
        $this->docBlock = $docBlock;
        $this->static = $static;
        $this->location = $location ?: new Location(-1);
        $this->endLocation = $endLocation ?: new Location(-1);
        $this->type = $type;
        $this->readOnly = $readOnly;

        if (is_string($default)) {
            trigger_error(
                'Default values for properties should be of type Expression, support for strings will be '
                . 'removed in 6.x',
                E_USER_DEPRECATED
            );
            $default = new Expression($default, []);
        }

        $this->default = $default;
    }

    /**
     * Returns the default value for this property.
     *
     * @return Expression|string|null
     */
    public function getDefault(bool $asString = true)
    {
        if ($this->default === null) {
            return null;
        }

        if ($asString) {
            trigger_error(
                'The Default value will become of type Expression by default',
                E_USER_DEPRECATED
            );

            return (string) $this->default;
        }

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
