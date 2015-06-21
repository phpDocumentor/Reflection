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

use Mockery as m;
use phpDocumentor\Reflection\Php\Factory\Argument;
use phpDocumentor\Reflection\Php\Factory\Class_;
use phpDocumentor\Reflection\Php\Factory\Constant;
use phpDocumentor\Reflection\Php\Factory\DocBlock as DocBlockFactory;
use phpDocumentor\Reflection\Php\Factory\File;
use phpDocumentor\Reflection\Php\Factory\Function_;
use phpDocumentor\Reflection\Php\Factory\Method;
use phpDocumentor\Reflection\Php\Factory\Property;
use phpDocumentor\Reflection\Php\NodesFactory;
use phpDocumentor\Reflection\Php\ProjectFactory;

/**
 * Intergration tests to check the correct working of processing a file into a project.
 *
 * @coversNothing
 */
class ProjectCreationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProjectFactory
     */
    private $fixture;

    protected function setUp()
    {
        //TODO replace this by a real factory
        $docblockFactory = m::mock(DocBlockFactoryInterface::class);
        $docblockFactory->shouldReceive('create')->andReturnNull();

        $this->fixture = new ProjectFactory(
            [
                new Argument(),
                new Class_(),
                new Constant(),
                new DocBlockFactory($docblockFactory),
                new File(new NodesFactory()),
                new Function_(),
                new Method(),
                new Property(new PrettyPrinter()),
            ]
        );
    }


    public function testCreateProjectWithFunctions()
    {
        $fileName = __DIR__ . '/project/simpleFunction.php';

        $project = $this->fixture->create([
            $fileName
        ]);

        $this->assertArrayHasKey($fileName, $project->getFiles());
        $this->assertArrayHasKey('\::simpleFunction()', $project->getFiles()[$fileName]->getFunctions());
    }

    public function testCreateProjectWithClass()
    {
        $fileName = __DIR__ . '/project/Pizza.php';
        $project = $this->fixture->create([
            $fileName
        ]);

        $this->assertArrayHasKey($fileName, $project->getFiles());
        $this->assertArrayHasKey('\\Pizza', $project->getFiles()[$fileName]->getClasses());
        $this->assertArrayHasKey('\\Pizza::PACKAGING', $project->getFiles()[$fileName]->getClasses()['\\Pizza']->getConstants());
    }

    public function testWithNamespacedClass()
    {
        $fileName = __DIR__ . '/project/Luigi/Pizza.php';
        $project = $this->fixture->create([
            $fileName
        ]);

        $this->assertArrayHasKey($fileName, $project->getFiles());
        $this->assertArrayHasKey('\\Luigi\\Pizza', $project->getFiles()[$fileName]->getClasses());
        $this->assertEquals('\Pizza', $project->getFiles()[$fileName]->getClasses()['\\Luigi\\Pizza']->getParent());
        $this->assertArrayHasKey(
            '\\Luigi\\Pizza::$instance',
            $project->getFiles()[$fileName]->getClasses()['\\Luigi\\Pizza']->getProperties()
        );

        $methods = $project->getFiles()[$fileName]->getClasses()['\\Luigi\\Pizza']->getMethods();
        $this->assertArrayHasKey(
            '\\Luigi\\Pizza::__construct()',
            $methods
        );

        $this->assertEquals('style', $methods['\\Luigi\\Pizza::__construct()']->getArguments()[0]->getName());
    }
}
