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
     * @var Null|DocBlock
     */
    private $dockBlock;

    /** @var string $value */
    protected $value;

    /**
     * Initializes the object.
     *
     * @param Fqsen $fqsen
     * @param DocBlock $docBlock
     * @param null $value
     */
    public function __construct(Fqsen $fqsen, DocBlock $docBlock = null, $value = null)
    {
        $this->fqsen = $fqsen;
        $this->dockBlock = $docBlock;
        $this->value = $value;
    }

    /**
     * {@inheritDoc}
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
     * Returns Docblock of this constant if available.
     *
     * @return Null|DocBlock
     */
    public function getDocBlock()
    {
        return $this->dockBlock;
    }
}
