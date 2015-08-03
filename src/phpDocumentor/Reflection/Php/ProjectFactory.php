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

use phpDocumentor\Reflection\Exception;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\ProjectFactory as ProjectFactoryInterface;

/**
 * Factory class to transform files into a project description.
 */
final class ProjectFactory implements ProjectFactoryInterface
{
    /**
     * @var ProjectFactoryStrategies[]
     */
    private $strategies;

    /**
     * Initializes the factory with a number of strategies.
     *
     * @param ProjectFactoryStrategy[] $strategies
     */
    public function __construct($strategies)
    {
        $this->strategies = new ProjectFactoryStrategies($strategies);
    }

    /**
     * Creates a project from the set of files.
     *
     * @param string[] $files
     * @return Project
     * @throws Exception when no matching strategy was found.
     */
    public function create(array $files)
    {
        $project = new Project('MyProject');

        foreach ($files as $filePath) {
            $strategy = $this->strategies->findMatching($filePath);
            $project->addFile($strategy->create($filePath, $this->strategies));
        }

        foreach ($project->getFiles() as $file) {
            foreach ($file->getNamespaces() as $namespaceFqsen) {
                $namespace = $this->getNamespaceByName($project, (string)$namespaceFqsen);
                foreach ($file->getClasses() as $class) {
                    if ($namespaceFqsen . '\\' . $class->getName() == $class->getFqsen()) {
                        $namespace->addClass($class->getFqsen());
                    }
                }

                foreach ($file->getInterfaces() as $interface) {
                    if ($namespaceFqsen . '\\' . $interface->getName() == $interface->getFqsen()) {
                        $namespace->addInterface($interface->getFqsen());
                    }
                }

                foreach ($file->getFunctions() as $function) {
                    if ($namespaceFqsen . '\\' . $function->getName() . '()' == $function->getFqsen()) {
                        $namespace->addFunction($function->getFqsen());
                    }
                }

                foreach ($file->getConstants() as $constant) {
                    if ($namespaceFqsen . '::' . $constant->getName() == $constant->getFqsen()) {
                        $namespace->addConstant($constant->getFqsen());
                    }
                }

                foreach ($file->getTraits() as $trait) {
                    if ($namespaceFqsen . '\\' . $trait->getName() == $trait->getFqsen()) {
                        $namespace->addTrait($trait->getFqsen());
                    }
                }
            }
        }

        return $project;
    }

    /**
     * Gets Namespace from the project if it exists, otherwise returns a new namepace
     *
     * @param Project $project
     * @param $name
     * @return Namespace_
     */
    private function getNamespaceByName(Project $project, $name)
    {
        $existingNamespaces = $project->getNamespaces();

        if (isset($existingNamespaces[$name])) {
            return $existingNamespaces[$name];
        }

        $namespace = new Namespace_(new Fqsen($name));
        $project->addNamespace($namespace);
        return $namespace;
    }
}