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
 * Descriptor representing a constant
 */
final class Constant implements Element
{
    /**
     * @var Fqsen
     */
    private $fqsen;

    /**
     * @var null|DocBlock
     */
    private $docBlock;

    /** @var null|string $value */
    protected $value;

    /**
     * Initializes the object.
     *
     * @param Fqsen $fqsen
     * @param DocBlock|null $docBlock
     * @param null|string $value
     */
    public function __construct(Fqsen $fqsen, DocBlock $docBlock = null, $value = null)
    {
        $this->fqsen = $fqsen;
        $this->docBlock = $docBlock;
        $this->value = $value;
    }

    /**
     * Returns the value of this constant.
     *
     * @return null|string
     */
    public function getValue()
    {
        return $this->value;
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
     * Returns DocBlock of this constant if available.
     *
     * @return null|DocBlock
     */
    public function getDocBlock()
    {
        return $this->docBlock;
    }
}
