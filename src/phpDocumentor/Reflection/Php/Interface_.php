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
     * @var DocBlock|null
     */
    private $docBlock;

    /** @var Constant[] $constants */
    protected $constants = array();

    /** @var Method[] $methods */
    protected $methods = array();

    /**
     * Initializes the object.
     *
     * @param Fqsen $fqsen
     * @param DocBlock $docBlock
     */
    public function __construct(Fqsen $fqsen, DocBlock $docBlock = null)
    {
        $this->fqsen = $fqsen;
        $this->docBlock = $docBlock;
    }

    /**
     * Returns the constants of this interface.
     *
     * @return Constant[]
     */
    public function getConstants()
    {
        return $this->constants;
    }

    /**
     * Add constant to this interface.
     *
     * @param Constant $constant
     * @return void
     */
    public function addConstant(Constant $constant)
    {
        $this->constants[(string)$constant->getFqsen()] = $constant;
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
     * @return void
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
     * Returns the DocBlock of this interface if available.
     *
     * @return null|DocBlock
     */
    public function getDocBlock()
    {
        return $this->docBlock;
    }
}
