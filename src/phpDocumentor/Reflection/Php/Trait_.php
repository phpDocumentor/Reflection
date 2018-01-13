<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;

/**
 * Descriptor representing a Trait.
 */
// @codingStandardsIgnoreStart
final class Trait_ implements Element
// @codingStandardsIgnoreEnd
{
    /**
     * @var Fqsen Full Qualified Structural Element Name
     */
    private $fqsen;

    /**
     * @var DocBlock|null
     */
    private $docBlock;

    /** @var Property[] $properties */
    private $properties = [];

    /** @var Method[] $methods */
    private $methods = [];

    /** @var Fqsen[] $usedTraits References to traits consumed by this trait */
    private $usedTraits = [];

    /**
     * @var Location
     */
    private $location;

    /**
     * Initializes the all properties
     *
     * @param DocBlock|null $docBlock
     */
    public function __construct(Fqsen $fqsen, DocBlock $docBlock = null, Location $location = null)
    {
        if ($location === null) {
            $location = new Location(-1);
        }

        $this->fqsen = $fqsen;
        $this->docBlock = $docBlock;
        $this->location = $location;
    }

    /**
     * Returns the methods of this Trait.
     *
     * @return Method[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Add a method to this Trait
     */
    public function addMethod(Method $method)
    {
        $this->methods[(string) $method->getFqsen()] = $method;
    }

    /**
     * Returns the properties of this trait.
     *
     * @return Property[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Add a property to this Trait.
     */
    public function addProperty(Property $property)
    {
        $this->properties[(string) $property->getFqsen()] = $property;
    }

    /**
     * Returns the Fqsen of the element.
     *
     * @return Fqsen
     */
    public function getFqsen()
    {
        return $this->fqsen;
    }

    /**
     * Returns the name of the element.
     *
     * @return string
     */
    public function getName()
    {
        return $this->fqsen->getName();
    }

    /**
     * @return null|DocBlock
     */
    public function getDocBlock()
    {
        return $this->docBlock;
    }

    /**
     * Returns fqsen of all traits used by this trait.
     *
     * @return Fqsen[]
     */
    public function getUsedTraits()
    {
        return $this->usedTraits;
    }

    /**
     * Add reference to trait used by this trait.
     */
    public function addUsedTrait(Fqsen $fqsen)
    {
        $this->usedTraits[(string) $fqsen] = $fqsen;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }
}
