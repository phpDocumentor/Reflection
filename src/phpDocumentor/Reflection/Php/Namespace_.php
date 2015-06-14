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

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\DocBlock;

/**
 * Represents a namespace and its children for a project.
 */
final class Namespace_ implements Element
{
    /**
     * @var Fqsen Full Qualified Structural Element Name
     */
    private $fqsen;

    /** @var Namespace_[] $namespaces */
    private $children = array();

    /** @var Function_[] $functions */
    private $functions = array();

    /** @var Constant[] $constants */
    private $constants = array();

    /** @var Class_[] $classes */
    private $classes = array();

    /** @var Interface_[] $interfaces */
    private $interfaces = array();

    /** @var Trait_[] $traits */
    private $traits = array();

    /**
     * Initializes the namespace.
     *
     * @param Fqsen $fqsen
     */
    public function __construct(Fqsen $fqsen)
    {
        $this->fqsen = $fqsen;
    }

    /**
     * Returns a list of all classes in this namespace.
     *
     * @return Collection
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * Add a class to this namespace.
     *
     * @param Class_ $class
     */
    public function addClass(Class_ $class)
    {
        $this->classes[(string)$class->getFqsen()] = $class;
    }

    /**
     * Returns a list of all constants in this namespace.
     *
     * @return Collection
     */
    public function getConstants()
    {
        return $this->constants;
    }

    /**
     * Add a Constant to this Namespace.
     *
     * @param Constant $contant
     */
    public function addConstant(Constant $contant)
    {
        $this->constants[(string)$contant->getFqsen()] = $contant;
    }

    /**
     * Returns a list of all functions in this namespace.
     *
     * @return Collection
     */
    public function getFunctions()
    {
        return $this->functions;
    }

    /**
     * Add a function to this namespace.
     *
     * @param Function_ $function
     */
    public function addFunction(Function_ $function)
    {
        $this->functions[(string)$function->getFqsen()] = $function;
    }

    /**
     * Returns a list of all interfaces in this namespace.
     *
     * @return Collection
     */
    public function getInterfaces()
    {
        return $this->interfaces;
    }

    /**
     * Add an interface the this namespace.
     *
     * @param Interface_ $interface
     */
    public function addInterface(Interface_ $interface)
    {
        $this->interfaces[(string)$interface->getFqsen()] = $interface;
    }

    /**
     * Returns a list of all namespaces contained in this namespace and its children.
     *
     * @return Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add a sub namespace to this namespace.
     *
     * @param Namespace_ $namespace
     */
    public function addChild(Namespace_ $namespace)
    {
        $this->children[(string)$namespace->getFqsen()] = $namespace;
    }

    /**
     * Returns a list of all traits in this namespace.
     *
     * @return Collection
     */
    public function getTraits()
    {
        return $this->traits;
    }

    /**
     * Add a trait to this namespace.
     *
     * @param Trait_ $trait
     */
    public function addTrait(Trait_ $trait)
    {
        $this->traits[(string)$trait->getFqsen()] = $trait;
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
