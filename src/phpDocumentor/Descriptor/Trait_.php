<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;

/**
 * Descriptor representing a Trait.
 */
final class Trait_ implements Element
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
    private $properties = array();

    /** @var Method[] $methods */
    private $methods = array();

    /**
     * Initializes the all properties
     *
     * @param Fqsen $fqsen
     * @param DocBlock|null $docBlock
     */
    public function __construct(Fqsen $fqsen, DocBlock $docBlock = null)
    {
        $this->fqsen = $fqsen;
        $this->docBlock = $docBlock;
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
     *
     * @param Method $method
     * @return void
     */
    public function addMethod(Method $method)
    {
        $this->methods[(string)$method->getFqsen()] = $method;
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
     *
     * @param Property $property
     * @return void
     */
    public function addProperty(Property $property)
    {
        $this->properties[(string)$property->getFqsen()] = $property;
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
}
