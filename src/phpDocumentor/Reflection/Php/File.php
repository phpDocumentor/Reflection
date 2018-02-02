<?php
declare(strict_types=1);

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
    public function __construct(string $hash, string $path, string $source = null, DocBlock $docBlock = null)
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
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * Retrieves the contents of this file.
     *
     * @return string|null
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * Returns the namespace fqsens that have been defined in this file.
     *
     * @return Fqsen[]
     */
    public function getNamespaces(): array
    {
        return $this->namespaces;
    }

    /**
     * Add namespace to file
     */
    public function addNamespace(Fqsen $fqsen): void
    {
        $this->namespaces[(string) $fqsen] = $fqsen;
    }

    /**
     * Returns a list of all includes that have been declared in this file.
     *
     * @return string[]
     */
    public function getIncludes(): array
    {
        return $this->includes;
    }

    /**
     * @param string $include
     */
    public function addInclude(string $include): void
    {
        $this->includes[$include] = $include;
    }

    /**
     * Returns a list of constant descriptors contained in this file.
     *
     * @return Constant[]
     */
    public function getConstants(): array
    {
        return $this->constants;
    }

    /**
     * Add constant to this file.
     */
    public function addConstant(Constant $constant): void
    {
        $this->constants[(string) $constant->getFqsen()] = $constant;
    }

    /**
     * Returns a list of function descriptors contained in this file.
     *
     * @return Function_[]
     */
    public function getFunctions(): array
    {
        return $this->functions;
    }

    /**
     * Add function to this file.
     */
    public function addFunction(Function_ $function): void
    {
        $this->functions[(string) $function->getFqsen()] = $function;
    }

    /**
     * Returns a list of class descriptors contained in this file.
     *
     * @return Class_[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * Add Class to this file.
     */
    public function addClass(Class_ $class): void
    {
        $this->classes[(string) $class->getFqsen()] = $class;
    }

    /**
     * Returns a list of interface descriptors contained in this file.
     *
     * @return Interface_[]
     */
    public function getInterfaces(): array
    {
        return $this->interfaces;
    }

    /**
     * Add interface to this file.
     */
    public function addInterface(Interface_ $interface): void
    {
        $this->interfaces[(string) $interface->getFqsen()] = $interface;
    }

    /**
     * Returns a list of trait descriptors contained in this file.
     *
     * @return Trait_[]
     */
    public function getTraits(): array
    {
        return $this->traits;
    }

    /**
     * Add trait to this file.
     */
    public function addTrait(Trait_ $trait): void
    {
        $this->traits[(string) $trait->getFqsen()] = $trait;
    }

    /**
     * Returns the file path relative to the project's root.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Returns the DocBlock of the element if available
     *
     * @return null|DocBlock
     */
    public function getDocBlock(): ?DocBlock
    {
        return $this->docBlock;
    }

    /**
     * Returns the full name of this file
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
