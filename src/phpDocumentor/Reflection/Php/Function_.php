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
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Mixed_;

/**
 * Descriptor representing a function
 */
// @codingStandardsIgnoreStart
final class Function_ implements Element
// // @codingStandardsIgnoreEnd
{
    /** @var Fqsen Full Qualified Structural Element Name */
    private $fqsen;

    /** @var Argument[] */
    private $arguments = [];

    /** @var DocBlock|null */
    private $docBlock;

    /** @var Location */
    private $location;

    /** @var Type */
    private $returnType;

    /**
     * Initializes the object.
     */
    public function __construct(
        Fqsen $fqsen,
        ?DocBlock $docBlock = null,
        ?Location $location = null,
        ?Type $returnType = null
    ) {
        if ($location === null) {
            $location = new Location(-1);
        }

        if ($returnType === null) {
            $returnType = new Mixed_();
        }

        $this->fqsen      = $fqsen;
        $this->docBlock   = $docBlock;
        $this->location   = $location;
        $this->returnType = $returnType;
    }

    /**
     * Returns the arguments of this function.
     *
     * @return Argument[]
     */
    public function getArguments() : array
    {
        return $this->arguments;
    }

    /**
     * Add an argument to the function.
     */
    public function addArgument(Argument $argument) : void
    {
        $this->arguments[] = $argument;
    }

    /**
     * Returns the Fqsen of the element.
     */
    public function getFqsen() : Fqsen
    {
        return $this->fqsen;
    }

    /**
     * Returns the name of the element.
     */
    public function getName() : string
    {
        return $this->fqsen->getName();
    }

    /**
     * Returns the DocBlock of the element if available
     */
    public function getDocBlock() : ?DocBlock
    {
        return $this->docBlock;
    }

    public function getLocation() : Location
    {
        return $this->location;
    }

    public function getReturnType() : Type
    {
        return $this->returnType;
    }
}
