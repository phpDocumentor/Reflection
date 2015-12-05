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

use phpDocumentor\Reflection\File as FileInterface;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Project as ProjectInterface;

/**
 * Represents the entire project with its files, namespaces and indexes.
 */
final class Project implements ProjectInterface
{
    /** @var string $name */
    private $name = '';

    /** @var Namespace_ $rootNamespace */
    private $rootNamespace;

    /**
     * @var FileInterface[]
     */
    private $files = array();

    /**
     * @var Namespace_[]
     */
    private $namespaces = array();

    /**
     * Initializes this descriptor.
     *
     * @param string $name Name of the current project.
     * @param Namespace_ $namespace Root namespace of the project.
     */
    public function __construct($name, Namespace_ $namespace = null)
    {
        $this->name = $name;
        $this->rootNamespace = $namespace;
        if ($this->rootNamespace === null) {
            $this->rootNamespace = new Namespace_(new Fqsen('\\'));
        }
    }

    /**
     * Returns the name of this project.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns all files with their sub-elements.
     *
     * @return FileInterface[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Add a file to this project.
     *
     * @param FileInterface $file
     */
    public function addFile(FileInterface $file)
    {
        $this->files[$file->getPath()] = $file;
    }

    /**
     * Returns all namespaces with their sub-elements.
     *
     * @return Namespace_[]
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }

    /**
     * Add a namespace to the project.
     *
     * @param Namespace_ $namespace
     */
    public function addNamespace(Namespace_ $namespace)
    {
        $this->namespaces[(string)$namespace->getFqsen()] = $namespace;
    }

    /**
     * Returns the root (global) namespace.
     *
     * @return Namespace_
     */
    public function getRootNamespace()
    {
        return $this->rootNamespace;
    }
}
