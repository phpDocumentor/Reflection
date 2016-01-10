<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;

/**
 * Descriptor representing a Class.
 */
// @codingStandardsIgnoreStart
final class Class_ implements Element
// @codingStandardsIgnoreEnd
{
    /**
     * @var Fqsen Full Qualified Structural Element Name
     */
    private $fqsen;

    /**
     * @var DocBlock|null
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

    /** @var Fqsen[] $implements References to interfaces that are implemented by this class. */
    private $implements = array();

    /** @var Constant[] $constants References to constants defined in this class. */
    private $constants = array();

    /** @var Property[] $properties References to properties defined in this class. */
    private $properties = array();

    /** @var Method[] $methods References to methods defined in this class. */
    private $methods = array();

    /** @var Fqsen[] $usedTraits References to traits consumed by this class */
    private $usedTraits = array();

    /**
     * Initializes a number of properties with the given values. Others are initialized by definition.
     *
     * @param Fqsen $fqsen
     * @param DocBlock $docBlock
     * @param Fqsen $parent
     * @param bool $abstract
     * @param bool $final
     */
    public function __construct(
        Fqsen $fqsen,
        DocBlock $docBlock = null,
        Fqsen $parent = null,
        $abstract = false,
        $final = false
    ) {
        $this->fqsen = $fqsen;
        $this->parent = $parent;
        $this->docBlock = $docBlock;
        $this->abstract = $abstract;
        $this->final = $final;
    }

    /**
     * Returns true when this class is final. Otherwise returns false.
     *
     * @return bool
     */
    public function isFinal()
    {
        return $this->final;
    }

    /**
     * Returns true when this class is abstract. Otherwise returns false.
     *
     * @return bool
     */
    public function isAbstract()
    {
        return $this->abstract;
    }

    /**
     * Returns the superclass this class is extending if available.
     *
     * @return NUll|Fqsen
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Returns the interfaces this class is implementing.
     *
     * @return Fqsen[]
     */
    public function getInterfaces()
    {
        return $this->implements;
    }

    /**
     * Add a interface Fqsen this class is implementing.
     *
     * @param Fqsen $interface
     * @return void
     */
    public function addInterface(Fqsen $interface)
    {
        $this->implements[(string)$interface] = $interface;
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
     * @return void
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
     * @return void
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
     * @return void
     */
    public function addProperty(Property $property)
    {
        $this->properties[(string)$property->getFqsen()] = $property;
    }

    /**
     * Returns the traits used by this class.
     *
     * @return Fqsen[]
     */
    public function getUsedTraits()
    {
        return $this->usedTraits;
    }

    /**
     * Add trait fqsen used by this class.
     *
     * @param Fqsen $fqsen
     */
    public function addUsedTrait(Fqsen $fqsen)
    {
        $this->usedTraits[(string)$fqsen] = $fqsen;
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
     * @returns null|DocBlock
     */
    public function getDocblock()
    {
        return $this->docBlock;
    }
}
