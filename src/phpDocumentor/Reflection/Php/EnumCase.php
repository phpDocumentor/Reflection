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

    private Fqsen $fqsen;

    private ?DocBlock $docBlock;

    private Location $location;

    private Location $endLocation;

    /** @var Expression|string|null */
    private $value;

    /**
     * @param Expression|string|null $value
     */
    public function __construct(
        Fqsen $fqsen,
        ?DocBlock $docBlock,
        ?Location $location = null,
        ?Location $endLocation = null,
        $value = null
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
        if (is_string($value)) {
            trigger_error(
                'Expression values for enum cases should be of type Expression, support for strings will be '
                . 'removed in 6.x',
                E_USER_DEPRECATED
            );
            $value = new Expression($value, []);
        }
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

    /**
     * Returns the value for this enum case.
     *
     * @return Expression|string|null
     */
    public function getValue(bool $asString = true)
    {
        if ($asString) {
            trigger_error(
                'The enum case value will become of type Expression by default',
                E_USER_DEPRECATED
            );

            return (string) $this->value;
        }

        return $this->value;
    }
}
