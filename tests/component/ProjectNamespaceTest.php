<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection;

use Mockery as m;
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\Factory\File;
use phpDocumentor\Reflection\Php\ProjectFactory;
use PHPUnit\Framework\TestCase;

/**
 * Intergration tests to check the correct working of processing a namespace into a project.
 *
 * @coversNothing
 */
class ProjectNamespaceTest extends TestCase
{
    /**
     * @var ProjectFactory
     */
    private $fixture;

    protected function setUp()
    {
        $this->fixture = $this->fixture = ProjectFactory::createInstance();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testWithNamespacedClass()
    {
        $fileName = __DIR__ . '/project/Luigi/Pizza.php';
        $project = $this->fixture->create('My Project', [
            new LocalFile($fileName),
        ]);

        $this->assertArrayHasKey($fileName, $project->getFiles());
        $this->assertArrayHasKey('\\Luigi', $project->getNamespaces());
        $this->assertEquals(
            ['\\Luigi\\Pizza' => new Fqsen('\\Luigi\\Pizza')],
            $project->getNamespaces()['\\Luigi']->getClasses()
        );
    }
}
