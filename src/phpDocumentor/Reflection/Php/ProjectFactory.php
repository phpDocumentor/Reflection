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

use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Exception;
use phpDocumentor\Reflection\File as SourceFile;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Project as ProjectInterface;
use phpDocumentor\Reflection\ProjectFactory as ProjectFactoryInterface;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;

/**
 * Factory class to transform files into a project description.
 */
final class ProjectFactory implements ProjectFactoryInterface
{
    /** @var ProjectFactoryStrategies */
    private $strategies;

    /**
     * Initializes the factory with a number of strategies.
     *
     * @param ProjectFactoryStrategy[] $strategies
     */
    public function __construct(array $strategies)
    {
        $this->strategies = new ProjectFactoryStrategies($strategies);
    }

    /**
     * Creates a new instance of this factory. With all default strategies.
     */
    public static function createInstance() : self
    {
        return new static(
            [
                new Factory\Argument(new PrettyPrinter()),
                new Factory\Class_(),
                new Factory\Define(new PrettyPrinter()),
                new Factory\GlobalConstant(new PrettyPrinter()),
                new Factory\ClassConstant(new PrettyPrinter()),
                new Factory\DocBlock(DocBlockFactory::createInstance()),
                new Factory\File(NodesFactory::createInstance()),
                new Factory\Function_(),
                new Factory\Interface_(),
                new Factory\Method(),
                new Factory\Property(new PrettyPrinter()),
                new Factory\Trait_(),
            ]
        );
    }

    /**
     * Creates a project from the set of files.
     *
     * @param string $name
     * @param SourceFile[] $files
     *
     * @throws Exception When no matching strategy was found.
     */
    public function create($name, array $files) : ProjectInterface
    {
        $project = new Project($name);

        foreach ($files as $filePath) {
            $strategy = $this->strategies->findMatching($filePath);
            $file     = $strategy->create($filePath, $this->strategies);
            if (!$file instanceof File) {
                continue;
            }

            $project->addFile($file);
        }

        $this->buildNamespaces($project);

        return $project;
    }

    /**
     * Builds the namespace tree with all elements in the project.
     */
    private function buildNamespaces(Project $project) : void
    {
        foreach ($project->getFiles() as $file) {
            foreach ($file->getNamespaces() as $namespaceFqsen) {
                $namespace = $this->getNamespaceByName($project, (string) $namespaceFqsen);
                $this->buildNamespace($file, $namespace);
            }
        }
    }

    /**
     * Gets Namespace from the project if it exists, otherwise returns a new namepace
     */
    private function getNamespaceByName(Project $project, string $name) : Namespace_
    {
        $existingNamespaces = $project->getNamespaces();

        if (isset($existingNamespaces[$name])) {
            return $existingNamespaces[$name];
        }

        $namespace = new Namespace_(new Fqsen($name));
        $project->addNamespace($namespace);
        return $namespace;
    }

    /**
     * Adds all elements belonging to the namespace to the namespace.
     */
    private function buildNamespace(File $file, Namespace_ $namespace) : void
    {
        foreach ($file->getClasses() as $class) {
            if ($namespace->getFqsen() . '\\' . $class->getName() !== (string) $class->getFqsen()) {
                continue;
            }

            $namespace->addClass($class->getFqsen());
        }

        foreach ($file->getInterfaces() as $interface) {
            if ($namespace->getFqsen() . '\\' . $interface->getName() !== (string) $interface->getFqsen()) {
                continue;
            }

            $namespace->addInterface($interface->getFqsen());
        }

        foreach ($file->getFunctions() as $function) {
            if ($namespace->getFqsen() . '\\' . $function->getName() . '()' !== (string) $function->getFqsen()) {
                continue;
            }

            $namespace->addFunction($function->getFqsen());
        }

        foreach ($file->getConstants() as $constant) {
            if ($namespace->getFqsen() . '::' . $constant->getName() !== (string) $constant->getFqsen() &&
                $namespace->getFqsen() . '\\' . $constant->getName() !== (string) $constant->getFqsen()) {
                continue;
            }

            $namespace->addConstant($constant->getFqsen());
        }

        foreach ($file->getTraits() as $trait) {
            if ($namespace->getFqsen() . '\\' . $trait->getName() !== (string) $trait->getFqsen()) {
                continue;
            }

            $namespace->addTrait($trait->getFqsen());
        }
    }
}
