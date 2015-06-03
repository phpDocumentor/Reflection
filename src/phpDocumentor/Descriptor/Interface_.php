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
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\DocBlock;

/**
 * Descriptor representing an Interface.
 */
final class Interface_ implements Element
{
    /**
     * @var Fqsen Full Qualified Structural Element Name
     */
    private $fqsen;

    /**
     * @var DocBlock | Null
     */
    private $docBlock;

    /** @var ConstantDescriptor[] $constants */
    protected $constants = array();

    /** @var Method[] $methods */
    protected $methods = array();

    /**
     * Initializes the all properties representing a collection with a new Collection object.
     */
    public function __construct(Fqsen $fqsen, DocBlock $docBlock = null)
    {
        $this->fqsen = $fqsen;
        $this->docBlock = $docBlock;
    }

    /**
     * Returns the constants of this interface.
     *
     * @return ConstantDescriptor[]
     */
    public function getConstants()
    {
        return $this->constants;
    }

    /**
     * Add constant to this interface.
     *
     * @param ConstantDescriptor $constant
     */
    public function addConstant(ConstantDescriptor $constant)
    {
        $this->constants[$constant->getFullyQualifiedStructuralElementName()] = $constant;
    }


    /**
     * Returns the methods in this interface.
     *
     * @return Method[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Add method to this interface.
     *
     * @param Method $method
     */
    public function addMethod(Method $method)
    {
        $this->methods[(string)$method->getFqsen()] = $method;
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
     * @return Null|DocBlock
     */
    public function getDocBlock()
    {
        return $this->docBlock;
    }
}
