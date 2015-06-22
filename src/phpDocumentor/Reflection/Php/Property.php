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

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Visibility;

/**
 * Descriptor representing a property.
 */
final class Property implements Element
{
    /**
     * @var Fqsen
     */
    private $fqsen;

    /**
     * @var DocBlock|null
     */
    private $docBlock;

    /** @var string[] $types */
    private $types = array();

    /** @var string $default */
    private $default = null;

    /** @var bool $static */
    private $static = false;

    /** @var Visibility $visibility */
    private $visibility;

    /**
     * @param Fqsen $fqsen
     * @param Visibility|null $visibility when null is provided a default 'public' is set.
     * @param DocBlock|null $docBlock
     * @param null|string $default
     * @param bool $static
     */
    public function __construct(Fqsen $fqsen, Visibility $visibility = null, DocBlock $docBlock = null, $default = null, $static = false)
    {
        $this->fqsen = $fqsen;
        $this->visibility = $visibility;
        $this->docBlock = $docBlock;
        $this->default = $default;
        $this->static = $static;

        if ($this->visibility === null) {
            $this->visibility = new Visibility('public');
        }
    }

    /**
     * returns the default value of this property.
     *
     * @return string
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Returns true when this method is static. Otherwise returns false.
     *
     * @return bool
     */
    public function isStatic()
    {
        return $this->static;
    }

    /**
     * Returns the types of this property.
     *
     * @return string[]
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Add a type to this property
     *
     * @param string $type
     * @return void
     */
    public function addType($type)
    {
        $this->types[] = $type;
    }

    /**
     * Return visibility of the property.
     *
     * @return Visibility
     */
    public function getVisibility()
    {
        return $this->visibility;
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
     * Returns the DocBlock of this property.
     *
     * @return DocBlock|null
     */
    public function getDocBlock()
    {
        return $this->docBlock;
    }
}
