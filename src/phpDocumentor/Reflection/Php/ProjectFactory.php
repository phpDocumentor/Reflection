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

use phpDocumentor\Descriptor\Project;
use phpDocumentor\Reflection\Exception;
use phpDocumentor\Reflection\ProjectFactory as ProjectFactoryInterface;

/**
 * Factory class to transform files into a project description.
 */
final class ProjectFactory implements ProjectFactoryInterface
{
    /**
     * @var ProjectFactoryStrategyContainer[]
     */
    private $strategies;

    /**
     * Initializes the factory with a number of strategies.
     *
     * @param ProjectFactoryStrategy[] $strategies
     */
    public function __construct($strategies)
    {
        $this->strategies = new ProjectFactoryStrategyContainer($strategies);
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
            $strategy = $this->strategies->findMatching($filePath);
            $project->addFile($strategy->create($filePath, $this->strategies));
        }

        return $project;
    }
}