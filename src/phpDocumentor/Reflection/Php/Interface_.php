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
use Webmozart\Assert\Assert;

/**
 * Descriptor representing an Interface.
 *
 * @api
 */
final class Interface_ implements Element, MetaDataContainerInterface, AttributeContainer
{
    use MetadataContainer;
    use HasAttributes;

    /** @var Constant[] */
    private array $constants = [];

    /** @var Method[] */
    private array $methods = [];

    private readonly Location $location;

    private readonly Location $endLocation;

    /**
     * Initializes the object.
     *
     * @param Fqsen[] $parents
     */
    public function __construct(
        /** @var Fqsen Full Qualified Structural Element Name */
        private readonly Fqsen $fqsen,
        private array $parents = [],
        private readonly DocBlock|null $docBlock = null,
        Location|null $location = null,
        Location|null $endLocation = null,
    ) {
        Assert::allIsInstanceOf($parents, Fqsen::class);
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
     * Returns the DocBlock of this interface if available.
     */
    public function getDocBlock(): DocBlock|null
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
