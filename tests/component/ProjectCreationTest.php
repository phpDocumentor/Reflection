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

class ProjectCreationTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateProjectWithFunctions()
    {
        $projectFactory = new ProjectFactory(
            [
                new File(new NodesFactory()),
                new Function_(),
            ]
        );

        $project = $projectFactory->create([
            __DIR__ . '/projects/functionProject/example.php'
        ]);

        $this->assertArrayHasKey(__DIR__ . '/projects/functionProject/example.php', $project->getFiles());
    }
}
