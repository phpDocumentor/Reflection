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
     */
    public function create($files)
    {
        $project = new Project('MyProject');

        foreach ($files as $filePath) {
            $project->addFile(new File('some-hash', $filePath));
        }

        return $project;
    }
}