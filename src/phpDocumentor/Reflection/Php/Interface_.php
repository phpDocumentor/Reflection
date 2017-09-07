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
use phpDocumentor\Reflection\Location;

/**
 * Descriptor representing an Interface.
 */
// @codingStandardsIgnoreStart
final class Interface_ implements Element
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

    /** @var Constant[] $constants */
    protected $constants = array();

    /** @var Method[] $methods */
    protected $methods = array();

    /** @var Fqsen[] $parents */
    protected $parents = array();

    /**
     * @var Location
     */
    private $location;

    /**
     * Initializes the object.
     *
     * @param Fqsen $fqsen
     * @param Fqsen[] $parents
     * @param DocBlock $docBlock
     */
    public function __construct(
        Fqsen $fqsen,
        array $parents = array(),
        DocBlock $docBlock = null,
        Location $location = null
    ) {
        if ($location === null) {
            $location = new Location(-1);
        }

        $this->fqsen = $fqsen;
        $this->docBlock = $docBlock;
        $this->parents = $parents;
        $this->location = $location;
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

    /**
     * Returns the Fqsen of the interfaces this interface is extending.
     *
     * @return Fqsen[]
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }
}
