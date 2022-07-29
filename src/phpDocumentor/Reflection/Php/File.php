<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Metadata\MetaDataContainer as MetaDataContainerInterface;

use function basename;

/**
 * Represents a file in the project.
 */
final class File implements MetaDataContainerInterface
{
    use MetadataContainer;

    private ?DocBlock $docBlock = null;

    private string $hash;

    private string $name;

    private string $path;

    private string $source;

    /** @var Fqsen[] */
    private array $namespaces = [];

    /** @var string[] */
    private array $includes = [];

    /** @var Function_[] */
    private array $functions = [];

    /** @var Constant[] */
    private array $constants = [];

    /** @var Class_[] */
    private array $classes = [];

    /** @var Interface_[] */
    private array $interfaces = [];

    /** @var Trait_[] */
    private array $traits = [];

    /** @var Enum_[] */
    private array $enums = [];

    /**
     * Initializes a new file descriptor with the given hash of its contents.
     *
     * @param string $hash An MD5 hash of the contents if this file.
     */
    public function __construct(string $hash, string $path, string $source = '', ?DocBlock $docBlock = null)
    {
        $this->hash     = $hash;
        $this->path     = $path;
        $this->name     = basename($path);
        $this->source   = $source;
        $this->docBlock = $docBlock;
    }

    /**
     * Returns the hash of the contents for this file.
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * Retrieves the contents of this file.
     */
    public function getSource(): string
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

    public function addEnum(Enum_ $enum): void
    {
        $this->enums[(string) $enum->getFqsen()] = $enum;
    }

    /**
     * Returns a list of enum descriptors contained in this file.
     *
     * @return Enum_[]
     */
    public function getEnums(): array
    {
        return $this->enums;
    }

    /**
     * Returns the file path relative to the project's root.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Returns the DocBlock of the element if available
     */
    public function getDocBlock(): ?DocBlock
    {
        return $this->docBlock;
    }

    /**
     * Returns the full name of this file
     */
    public function getName(): string
    {
        return $this->name;
    }
}
