<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2015 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php;


use phpDocumentor\Descriptor\File;
use phpDocumentor\Descriptor\Project;
use phpDocumentor\Reflection\Exception;

final class ProjectFactory
{
    /**
     * @var ProjectFactoryStrategy[]
     */
    private $strategies;

    /**
     * Initializes the factory with a number of strategies.
     *
     * @param ProjectFactoryStrategy[] $strategies
     */
    public function __construct($strategies)
    {
        foreach ($strategies as $strategy) {
            if (!$strategy instanceof ProjectFactoryStrategy) {
                throw new \InvalidArgumentException(
                    sprintf(
                        '%s is not implementing %s',
                        get_class($strategy),
                        ProjectFactoryStrategy::class
                    )
                );
            }
        }

        $this->strategies = $strategies;
    }

    /**
     * Creates a project from the set of files.
     *
     * @param string[] $files
     * @return Project
     * @throws Exception when no matching strategy was found.
     */
    public function create($files)
    {
        $project = new Project('MyProject');

        foreach ($files as $filePath) {
            $strategy = $this->findMatchingStrategy($filePath);
            $project->addFile($strategy->create($filePath, $this));
        }

        return $project;
    }

    /**
     * Find the ProjectFactoryStrategy that matches $object.
     *
     * @param mixed $object
     * @return ProjectFactoryStrategy
     * @throws Exception when no matching strategy was found.
     */
    private function findMatchingStrategy($object)
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->matches($object)) {
                return $strategy;
            }
        }

        throw new Exception(
            sprintf(
                'No matching factory found for %s',
                is_object($object) ? get_class($object) : gettype($object)
            )
        );
    }
}