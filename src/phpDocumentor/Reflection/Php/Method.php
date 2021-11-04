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
use PhpParser\Node\Stmt;

/**
 * Descriptor representing a Method in a Class, Interface or Trait.
 */
final class Method implements Element, MetaDataContainerInterface
{
    use MetadataContainer;

    /** @var Stmt */
    protected $node;

    /** @var DocBlock|null documentation of this method. */
    private $docBlock = null;

    /** @var Fqsen Full Qualified Structural Element Name */
    private $fqsen;

    /** @var bool */
    private $abstract = false;

    /** @var bool */
    private $final = false;

    /** @var bool */
    private $static = false;

    /** @var Visibility|null visibility of this method */
    private $visibility = null;

    /** @var Argument[] */
    private $arguments = [];

    /** @var Location */
    private $location;

    /** @var Type */
    private $returnType;

    /**
     * Initializes the all properties.
     *
     * @param Visibility|null $visibility when null is provided a default 'public' is set.
     */
    public function __construct(
        Stmt $node,
        Fqsen $fqsen,
        ?Visibility $visibility = null,
        ?DocBlock $docBlock = null,
        bool $abstract = false,
        bool $static = false,
        bool $final = false,
        ?Location $location = null,
        ?Type $returnType = null
    ) {
        $this->node       = $node;
        $this->fqsen      = $fqsen;
        $this->visibility = $visibility;
        $this->docBlock   = $docBlock;

        if ($this->visibility === null) {
            $this->visibility = new Visibility('public');
        }

        if ($location === null) {
            $location = new Location(-1);
        }

        if ($returnType === null) {
            $returnType = new Mixed_();
        }

        $this->abstract   = $abstract;
        $this->static     = $static;
        $this->final      = $final;
        $this->location   = $location;
        $this->returnType = $returnType;
    }

    /**
     * Returns the current PHP-Parser node that holds more detailed information
     * about the reflected object. e.g. position in the file and further attributes.
     * @return Stmt
     */
    public function getNode(): Stmt
    {
        return $this->node;
    }

    /**
     * Returns true when this method is abstract. Otherwise returns false.
     */
    public function isAbstract(): bool
    {
        return $this->abstract;
    }

    /**
     * Returns true when this method is final. Otherwise returns false.
     */
    public function isFinal(): bool
    {
        return $this->final;
    }

    /**
     * Returns true when this method is static. Otherwise returns false.
     */
    public function isStatic(): bool
    {
        return $this->static;
    }

    /**
     * Returns the Visibility of this method.
     */
    public function getVisibility(): ?Visibility
    {
        return $this->visibility;
    }

    /**
     * Returns the arguments of this method.
     *
     * @return Argument[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Add new argument to this method.
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
     * Returns the DocBlock of this method if available.
     *
     * @returns null|DocBlock
     */
    public function getDocBlock(): ?DocBlock
    {
        return $this->docBlock;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    /**
     * Returns the in code defined return type.
     *
     * Return types are introduced in php 7.0 when your could doesn't have a
     * return type defined this method will return Mixed_ by default. The return value of this
     * method is not affected by the return tag in your docblock.
     */
    public function getReturnType(): Type
    {
        return $this->returnType;
    }
}
