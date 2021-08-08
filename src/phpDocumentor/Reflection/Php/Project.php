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

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Project as ProjectInterface;

/**
 * Represents the entire project with its files, namespaces and indexes.
 */
final class Project implements ProjectInterface
{
    /** @var string */
    private $name = '';

    /** @var Namespace_|null */
    private $rootNamespace;

    /** @var File[] */
    private $files = [];

    /** @var Namespace_[] */
    private $namespaces = [];

    /**
     * Initializes this descriptor.
     *
     * @param string          $name      Name of the current project.
     * @param Namespace_|null $namespace Root namespace of the project.
     */
    public function __construct(string $name, ?Namespace_ $namespace = null)
    {
        $this->name          = $name;
        $this->rootNamespace = $namespace;
        if ($this->rootNamespace !== null) {
            return;
        }

        $this->rootNamespace = new Namespace_(new Fqsen('\\'));
    }

    /**
     * Returns the name of this project.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns all files with their sub-elements.
     *
     * @return File[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * Add a file to this project.
     */
    public function addFile(File $file): void
    {
        $this->files[$file->getPath()] = $file;
    }

    /**
     * Returns all namespaces with their sub-elements.
     *
     * @return Namespace_[]
     */
    public function getNamespaces(): array
    {
        return $this->namespaces;
    }

    /**
     * Add a namespace to the project.
     */
    public function addNamespace(Namespace_ $namespace): void
    {
        $this->namespaces[(string) $namespace->getFqsen()] = $namespace;
    }

    /**
     * Returns the root (global) namespace.
     */
    public function getRootNamespace(): ?Namespace_
    {
        return $this->rootNamespace;
    }
}
