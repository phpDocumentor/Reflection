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
use phpDocumentor\Reflection\Php\NodesFactory;
use phpDocumentor\Reflection\Php\Factory\Function_;
use phpDocumentor\Reflection\Php\Factory\Interface_;
use phpDocumentor\Reflection\Php\Factory\Method;
use phpDocumentor\Reflection\Php\Factory\Property;
use phpDocumentor\Reflection\Php\Factory\Trait_;
use phpDocumentor\Reflection\Php\ProjectFactory;

/**
 * Intergration tests to check the correct working of processing a namespace into a project.
 *
 * @coversNothing
 */
class ProjectNamespaceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProjectFactory
     */
    private $fixture;

    /**
     *
     */
    protected function setUp()
    {
        //TODO replace this by a real factory
        $docblockFactory = m::mock(DocBlockFactoryInterface::class);
        $docblockFactory->shouldReceive('create')->andReturnNull();

        $this->fixture = new ProjectFactory(
            array(
                new Argument(),
                new Class_(),
                new Constant(),
                new DocBlockFactory($docblockFactory),
                new File(new NodesFactory()),
                new Function_(),
                new Interface_(),
                new Method(),
                new Property(new PrettyPrinter()),
                new Trait_(),
            )
        );
    }

    public function testWithNamespacedClass()
    {
        $fileName = __DIR__ . '/project/Luigi/Pizza.php';
        $project = $this->fixture->create([
            $fileName
        ]);

        $this->assertArrayHasKey($fileName, $project->getFiles());
        $this->assertArrayHasKey('\\Luigi', $project->getNamespaces());
        $this->assertEquals(
            ['\\Luigi\\Pizza' => new Fqsen('\\Luigi\\Pizza')],
            $project->getNamespaces()['\\Luigi']->getClasses()
        );
    }
}
