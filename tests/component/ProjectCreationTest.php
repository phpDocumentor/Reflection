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

namespace phpDocumentor\Reflection;

use phpDocumentor\Reflection\Php\Factory\File;
use phpDocumentor\Reflection\Php\Factory\Function_;
use phpDocumentor\Reflection\Php\NodesFactory;
use phpDocumentor\Reflection\Php\ProjectFactory;

/**
 * Intergration tests to check the correct working of processing a file into a project.
 *
 * @coversNothing
 */
class ProjectCreationTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateProjectWithFunctions()
    {
        $fileName = __DIR__ . '/project/simpleFunction.php';
        $projectFactory = new ProjectFactory(
            [
                new File(new NodesFactory()),
                new Function_(),
            ]
        );

        $project = $projectFactory->create([
            $fileName
        ]);

        $this->assertArrayHasKey($fileName, $project->getFiles());
        $this->assertArrayHasKey('\::simpleFunction()', $project->getFiles()[$fileName]->getFunctions());
    }
}
