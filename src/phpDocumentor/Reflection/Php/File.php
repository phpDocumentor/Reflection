<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;

/**
 * Represents a file in the project.
 */
final class File
{
    /**
     * @var DocBlock|null
     */
    private $docBlock = null;

    /** @var string $hash */
    private $hash;

    /** @var string $name */
    private $name = null;

    /** @var string $path */
    private $path = null;

    /** @var string|null $source */
    private $source = null;

    /** @var Fqsen[] $namespaces */
    private $namespaces = [];

    /** @var string[] $includes */
    private $includes = [];

    /** @var Function_[] $functions */
    private $functions = [];

    /** @var Constant[] $constants */
    private $constants = [];

    /** @var Class_[] $classes */
    private $classes = [];

    /** @var Interface_[] $interfaces */
    private $interfaces = [];

    /** @var Trait_[] $traits */
    private $traits = [];

    /**
     * Initializes a new file descriptor with the given hash of its contents.
     *
     * @param string $hash An MD5 hash of the contents if this file.
     * @param string $path
     * @param string|null $source
     */
    public function __construct($hash, $path, $source = null, DocBlock $docBlock = null)
    {
        $this->hash = $hash;
        $this->path = $path;
        $this->name = basename($path);
        $this->source = $source;
        $this->docBlock = $docBlock;
    }

    /**
     * Returns the hash of the contents for this file.
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Retrieves the contents of this file.
     *
     * @return string|null
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Returns the namespace fqsens that have been defined in this file.
     *
     * @return Fqsen[]
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }

    /**
     * Add namespace to file
     */
    public function addNamespace(Fqsen $fqsen)
    {
        $this->namespaces[(string) $fqsen] = $fqsen;
    }

    /**
     * Returns a list of all includes that have been declared in this file.
     *
     * @return string[]
     */
    public function getIncludes()
    {
        return $this->includes;
    }

    /**
     * @param string $include
     */
    public function addInclude($include)
    {
        $this->includes[$include] = $include;
    }

    /**
     * Returns a list of constant descriptors contained in this file.
     *
     * @return Constant[]
     */
    public function getConstants()
    {
        return $this->constants;
    }

    /**
     * Add constant to this file.
     */
    public function addConstant(Constant $constant)
    {
        $this->constants[(string) $constant->getFqsen()] = $constant;
    }

    /**
     * Returns a list of function descriptors contained in this file.
     *
     * @return Function_[]
     */
    public function getFunctions()
    {
        return $this->functions;
    }

    /**
     * Add function to this file.
     */
    public function addFunction(Function_ $function)
    {
        $this->functions[(string) $function->getFqsen()] = $function;
    }

    /**
     * Returns a list of class descriptors contained in this file.
     *
     * @return Class_[]
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * Add Class to this file.
     */
    public function addClass(Class_ $class)
    {
        $this->classes[(string) $class->getFqsen()] = $class;
    }

    /**
     * Returns a list of interface descriptors contained in this file.
     *
     * @return Interface_[]
     */
    public function getInterfaces()
    {
        return $this->interfaces;
    }

    /**
     * Add interface to this file.
     */
    public function addInterface(Interface_ $interface)
    {
        $this->interfaces[(string) $interface->getFqsen()] = $interface;
    }

    /**
     * Returns a list of trait descriptors contained in this file.
     *
     * @return Trait_[]
     */
    public function getTraits()
    {
        return $this->traits;
    }

    /**
     * Add trait to this file.
     */
    public function addTrait(Trait_ $trait)
    {
        $this->traits[(string) $trait->getFqsen()] = $trait;
    }

    /**
     * Returns the file path relative to the project's root.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns the DocBlock of the element if available
     *
     * @return null|DocBlock
     */
    public function getDocBlock()
    {
        return $this->docBlock;
    }

    /**
     * Returns the full name of this file
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
