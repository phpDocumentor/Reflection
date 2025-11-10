<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php;

use Override;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Metadata\MetaDataContainer as MetaDataContainerInterface;

use function is_string;
use function trigger_error;

use const E_USER_DEPRECATED;

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
        private Expression|string|null $value = null,
    ) {
        if ($location === null) {
            $location = new Location(-1);
        }

        if ($endLocation === null) {
            $endLocation = new Location(-1);
        }

        $this->location = $location;
        $this->endLocation = $endLocation;
        if (!is_string($this->value)) {
            return;
        }

        trigger_error(
            'Expression values for enum cases should be of type Expression, support for strings will be '
            . 'removed in 7.x',
            E_USER_DEPRECATED,
        );
        $this->value = new Expression($this->value, []);
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

    /**
     * Returns the value for this enum case.
     */
    public function getValue(bool $asString = true): Expression|string|null
    {
        if ($this->value === null) {
            return null;
        }

        if ($asString) {
            trigger_error(
                'The enum case value will become of type Expression by default',
                E_USER_DEPRECATED,
            );

            return (string) $this->value;
        }

        return $this->value;
    }
}
