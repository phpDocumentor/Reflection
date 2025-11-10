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

use function is_string;
use function trigger_error;

use const E_USER_DEPRECATED;

/**
 * Descriptor representing a property.
 *
 * @api
 */
final class Property implements Element, MetaDataContainerInterface, AttributeContainer
{
    use MetadataContainer;
    use HasAttributes;

    /** @var string[] $types */
    private array $types = [];

    private Visibility|null $visibility = null;

    private readonly Location $location;

    private readonly Location $endLocation;

    /**
     * @param Visibility|null $visibility when null is provided a default 'public' is set.
     * @param PropertyHook[] $hooks
     */
    public function __construct(
        private readonly Fqsen $fqsen,
        Visibility|null $visibility = null,
        private readonly DocBlock|null $docBlock = null,
        private Expression|string|null $default = null,
        private readonly bool $static = false,
        Location|null $location = null,
        Location|null $endLocation = null,
        private readonly Type|null $type = null,
        private readonly bool $readOnly = false,
        private readonly array $hooks = [],
        private readonly bool $virtual = false,
    ) {
        $this->visibility = $visibility ?: new Visibility('public');
        $this->location = $location ?: new Location(-1);
        $this->endLocation = $endLocation ?: new Location(-1);

        if (!is_string($this->default)) {
            return;
        }

        trigger_error(
            'Default values for properties should be of type Expression, support for strings will be '
            . 'removed in 7.x',
            E_USER_DEPRECATED,
        );
        $this->default = new Expression($this->default, []);
    }

    /**
     * Returns the default value for this property.
     */
    public function getDefault(bool $asString = true): Expression|string|null
    {
        if ($this->default === null) {
            return null;
        }

        if ($asString) {
            trigger_error(
                'The Default value will become of type Expression by default',
                E_USER_DEPRECATED,
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
    public function getVisibility(): Visibility|null
    {
        return $this->visibility;
    }

    /**
     * Returns the Fqsen of the element.
     */
    #[Override]
    public function getFqsen(): Fqsen
    {
        return $this->fqsen;
    }

    /**
     * Returns the name of the element.
     */
    #[Override]
    public function getName(): string
    {
        return $this->fqsen->getName();
    }

    /**
     * Returns the DocBlock of this property.
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

    public function getType(): Type|null
    {
        return $this->type;
    }

    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    /** @return PropertyHook[] */
    public function getHooks(): array
    {
        return $this->hooks;
    }

    /**
     * Returns true when this property is virtual (not explicitly backed).
     *
     * A virtual property is one where no defined hook references the property itself.
     */
    public function isVirtual(): bool
    {
        return $this->virtual;
    }
}
