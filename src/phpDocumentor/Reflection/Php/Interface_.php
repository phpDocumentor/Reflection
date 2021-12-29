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
use Webmozart\Assert\Assert;

/**
 * Descriptor representing an Interface.
 */
final class Interface_ implements Element, MetaDataContainerInterface
{
    use MetadataContainer;

    /** @var Fqsen Full Qualified Structural Element Name */
    private $fqsen;

    /** @var DocBlock|null */
    private $docBlock;

    /** @var Constant[] */
    private $constants = [];

    /** @var Method[] */
    private $methods = [];

    /** @var Fqsen[] */
    private $parents = [];

    /** @var Location */
    private $location;

    /** @var Location */
    private $endLocation;

    /**
     * Initializes the object.
     *
     * @param Fqsen[] $parents
     */
    public function __construct(
        Fqsen $fqsen,
        array $parents = [],
        ?DocBlock $docBlock = null,
        ?Location $location = null,
        ?Location $endLocation = null
    ) {
        Assert::allIsInstanceOf($parents, Fqsen::class);

        $this->fqsen       = $fqsen;
        $this->docBlock    = $docBlock;
        $this->parents     = $parents;
        $this->location    = $location ?: new Location(-1);
        $this->endLocation = $endLocation ?: new Location(-1);
    }

    /**
     * Returns the constants of this interface.
     *
     * @return Constant[]
     */
    public function getConstants(): array
    {
        return $this->constants;
    }

    /**
     * Add constant to this interface.
     */
    public function addConstant(Constant $constant): void
    {
        $this->constants[(string) $constant->getFqsen()] = $constant;
    }

    /**
     * Returns the methods in this interface.
     *
     * @return Method[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Add method to this interface.
     */
    public function addMethod(Method $method): void
    {
        $this->methods[(string) $method->getFqsen()] = $method;
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
     * Returns the DocBlock of this interface if available.
     */
    public function getDocBlock(): ?DocBlock
    {
        return $this->docBlock;
    }

    /**
     * Returns the Fqsen of the interfaces this interface is extending.
     *
     * @return Fqsen[]
     */
    public function getParents(): array
    {
        return $this->parents;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getEndLocation(): Location
    {
        return $this->endLocation;
    }
}
