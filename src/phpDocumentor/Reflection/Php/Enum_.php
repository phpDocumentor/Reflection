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

final class Enum_ implements Element
{
    /** @var Fqsen Full Qualified Structural Element Name */
    private $fqsen;

    /** @var DocBlock|null */
    private $docBlock;

    /** @var Fqsen|null */
    private $parent;

    /** @var Location|null */
    private $location;

    /** @var EnumCase[] */
    private $cases = [];

    /** @var array<string, Fqsen> */
    private $implements = [];

    /** @var array<string, Method> */
    private $methods = [];

    /** @var array<string, Fqsen> */
    private $usedTraits = [];

    public function __construct(Fqsen $fqsen, ?DocBlock $docBlock = null, ?Fqsen $parent = null, ?Location $location = null)
    {
        if ($location === null) {
            $location = new Location(-1);
        }

        $this->fqsen    = $fqsen;
        $this->docBlock = $docBlock;
        $this->parent   = $parent;
        $this->location = $location;
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

    public function getParent(): ?Fqsen
    {
        return $this->parent;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function addCase(EnumCase $case): void
    {
        $this->cases[(string) $case->getFqsen()] = $case;
    }

    /** @return EnumCase[] */
    public function getCases(): array
    {
        return $this->cases;
    }

    /**
     * Returns the interfaces this enum is implementing.
     *
     * @return Fqsen[]
     */
    public function getInterfaces(): array
    {
        return $this->implements;
    }

    /**
     * Add an interface Fqsen this enum is implementing.
     */
    public function addInterface(Fqsen $interface): void
    {
        $this->implements[(string) $interface] = $interface;
    }

    /**
     * Returns the methods of this enum.
     *
     * @return Method[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Add a method to this enum.
     */
    public function addMethod(Method $method): void
    {
        $this->methods[(string) $method->getFqsen()] = $method;
    }

    /**
     * Returns the traits used by this enum.
     *
     * @return Fqsen[]
     */
    public function getUsedTraits(): array
    {
        return $this->usedTraits;
    }

    /**
     * Add trait fqsen used by this enum.
     */
    public function addUsedTrait(Fqsen $fqsen): void
    {
        $this->usedTraits[(string) $fqsen] = $fqsen;
    }
}
