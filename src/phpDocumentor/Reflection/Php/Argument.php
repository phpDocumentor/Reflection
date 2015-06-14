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

/**
 * Descriptor representing a single Argument of a method or function.
 */
final class Argument
{
    /**
     * @var string name of the Argument
     */
    private $name = null;

    /** @var string[] $type an array of normalized types that should be in this Argument */
    private $types = array();

    /** @var string|null $default the default value for an argument or null if none is provided */
    private $default = null;

    /** @var bool $byReference whether the argument passes the parameter by reference instead of by value */
    private $byReference = false;

    /** @var boolean Determines if this Argument represents a variadic argument */
    private $isVariadic = false;

    /**
     * Initializes the object.
     *
     * @param string $name
     * @param string $default
     * @param bool $byReference
     * @param bool $isVariadic
     */
    public function __construct($name, $default = null, $byReference = false, $isVariadic = false)
    {
        $this->name = $name;
        $this->default = $default;
        $this->byReference = $byReference;
        $this->isVariadic = $isVariadic;
    }

    /**
     * Returns the name of this argument.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Add a type.
     * @param string $type
     */
    public function addType($type)
    {
        $this->types[] = $type;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * {@inheritDoc}
     */
    public function isByReference()
    {
        return $this->byReference;
    }

    /**
     * Returns whether this argument represents a variadic argument.
     *
     * @return boolean
     */
    public function isVariadic()
    {
        return $this->isVariadic;
    }
}
