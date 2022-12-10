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

use function is_string;
use function trigger_error;

use const E_USER_DEPRECATED;

/**
 * Descriptor representing a constant
 */
final class Constant implements Element, MetaDataContainerInterface
{
    use MetadataContainer;

    private Fqsen $fqsen;

    private ?DocBlock $docBlock;

    /** @var string|Expression|null */
    private $value;

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
        $value = null,
        ?Location $location = null,
        ?Location $endLocation = null,
        ?Visibility $visibility = null,
        bool $final = false
    ) {
        $this->fqsen = $fqsen;
        $this->docBlock = $docBlock;
        $this->location = $location ?: new Location(-1);
        $this->endLocation = $endLocation ?: new Location(-1);
        $this->visibility = $visibility ?: new Visibility(Visibility::PUBLIC_);
        $this->final = $final;

        if (is_string($value)) {
            trigger_error(
                'Constant values should be of type Expression, support for strings will be '
                . 'removed in 6.x',
                E_USER_DEPRECATED
            );
            $value = new Expression($value, []);
        }

        $this->value = $value;
    }

    /**
     * Returns the expression value for this constant.
     *
     * @return Expression|string|null
     */
    public function getValue(bool $asString = true)
    {
        if ($this->value === null) {
            return null;
        }

        if ($asString) {
            trigger_error(
                'The expression value will become of type Expression by default',
                E_USER_DEPRECATED
            );

            return (string) $this->value;
        }

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
