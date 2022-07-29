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
use phpDocumentor\Reflection\Types\Mixed_;

/**
 * Descriptor representing a function
 */
// @codingStandardsIgnoreStart
final class Function_ implements Element, MetaDataContainerInterface
// // @codingStandardsIgnoreEnd
{
    use MetadataContainer;

    /** @var Fqsen Full Qualified Structural Element Name */
    private Fqsen $fqsen;

    /** @var Argument[] */
    private array $arguments = [];

    private ?DocBlock $docBlock;

    private Location $location;

    private Location $endLocation;

    private Type $returnType;

    private bool $hasReturnByReference;

    /**
     * Initializes the object.
     */
    public function __construct(
        Fqsen $fqsen,
        ?DocBlock $docBlock = null,
        ?Location $location = null,
        ?Location $endLocation = null,
        ?Type $returnType = null,
        bool $hasReturnByReference = false
    ) {
        if ($location === null) {
            $location = new Location(-1);
        }

        if ($endLocation === null) {
            $endLocation = new Location(-1);
        }

        if ($returnType === null) {
            $returnType = new Mixed_();
        }

        $this->fqsen                = $fqsen;
        $this->docBlock             = $docBlock;
        $this->location             = $location;
        $this->endLocation          = $endLocation;
        $this->returnType           = $returnType;
        $this->hasReturnByReference = $hasReturnByReference;
    }

    /**
     * Returns the arguments of this function.
     *
     * @return Argument[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Add an argument to the function.
     */
    public function addArgument(Argument $argument): void
    {
        $this->arguments[] = $argument;
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
     * Returns the DocBlock of the element if available
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

    public function getReturnType(): Type
    {
        return $this->returnType;
    }

    public function getHasReturnByReference(): bool
    {
        return $this->hasReturnByReference;
    }
}
