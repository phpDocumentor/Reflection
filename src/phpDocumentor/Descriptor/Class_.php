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
 * Descriptor representing a Class.
 */
final class Class_ implements Element
{
    /**
     * @var Fqsen Full Qualified Structural Element Name
     */
    private $fqsen;

    /**
     * @var DocBlock | Null
     */
    private $docBlock = null;

    /** @var boolean $abstract Whether this is an abstract class. */
    private $abstract = false;

    /** @var boolean $final Whether this class is marked as final and can't be subclassed. */
    private $final = false;

    /**
     * @var Class_ $parent The class this class is extending.
     */
    private $parent = null;

    /** @var Interface_[] $implements References to interfaces that are implemented by this class. */
    private $implements = array();

    /** @var Constant[] $constants References to constants defined in this class. */
    private $constants = array();

    /** @var Property[] $properties References to properties defined in this class. */
    private $properties = array();

    /** @var Method[] $methods References to methods defined in this class. */
    private $methods = array();

    /** @var Trait_[] $usedTraits References to traits consumed by this class */
    private $usedTraits = array();

    /**
     * Initializes the all properties representing a collection with a new Collection object.
     *
     * @param Fqsen $fqsen
     * @param DocBlock $docBlock
     * @param bool $abstract
     * @param bool $final
     */
    public function __construct(Fqsen $fqsen, DocBlock $docBlock = null, Class_ $parent = null, $abstract = false, $final = false)
    {
        $this->fqsen = $fqsen;
        $this->parent = $parent;
        $this->docBlock = $docBlock;
        $this->abstract = $abstract;
        $this->final = $final;
    }

    /**
     * Returns true when this method is final. Otherwise returns false.
     *
     * @return bool
     */
    public function isFinal()
    {
        return $this->final;
    }

    /**
     * Returns true when this method is abstract. Otherwise returns false.
     *
     * @return bool
     */
    public function isAbstract()
    {
        return $this->abstract;
    }

    /**
     * Returns the Class_ this class is extending if available.
     *
     * @return NUll|Class_
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Returns the interfaces this class is implementing.
     *
     * @return Interface_[]
     */
    public function getInterfaces()
    {
        return $this->implements;
    }

    /**
     * Add interface this class is implementing.
     *
     * @param Interface_ $interface
     */
    public function addInterface(Interface_ $interface)
    {
        $this->implements[(string)$interface->getFqsen()] = $interface;
    }

    /**
     * Returns the constants of this class.
     *
     * @return Constant[]
     */
    public function getConstants()
    {
        return $this->constants;
    }

    /**
     * Add Constant to this class.
     *
     * @param Constant $constant
     */
    public function addConstant(Constant $constant)
    {
        $this->constants[(string)$constant->getFqsen()] = $constant;
    }

    /**
     * Returns the methods of this class.
     *
     * @return Method[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Add a method to this class.
     *
     * @param Method $method
     */
    public function addMethod(Method $method)
    {
        $this->methods[(string)$method->getFqsen()] = $method;
    }

    /**
     * Returns the properties of this class.
     *
     * @return Property[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Add a property to this class.
     *
     * @param Property $property
     */
    public function addProperty(Property $property)
    {
        $this->properties[(string)$property->getFqsen()] = $property;
    }
    /**
     * Returns the traits used by this class.
     *
     * Returned values may either be a string (when the Trait is not in this project) or a Trait_.
     *
     * @return Trait_
     */
    public function getUsedTraits()
    {
        return $this->usedTraits;
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
     * @returns Null|DocBlock
     */
    public function getDocblock()
    {
        return $this->docBlock;
    }
}
