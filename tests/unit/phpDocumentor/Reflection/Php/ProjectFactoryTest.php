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

use Mockery as m;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Factory\DummyFactoryStrategy;

/**
 * Test case for ProjectFactory
 *
 * @coversDefaultClass phpDocumentor\Reflection\Php\ProjectFactory
 */
class ProjectFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testStrategiesAreChecked()
    {
        new ProjectFactory(array(new DummyFactoryStrategy()));
    }

    /**
     * @covers ::create
     * @covers ::<private>
     */
    public function testCreate()
    {
        $someOtherStrategy = m::mock(ProjectFactoryStrategy::class);
        $someOtherStrategy->shouldReceive('matches')->twice()->andReturn(false);
        $someOtherStrategy->shouldReceive('create')->never();

        $fileStrategyMock = m::mock(ProjectFactoryStrategy::class);
        $fileStrategyMock->shouldReceive('matches')->twice()->andReturn(true);
        $fileStrategyMock->shouldReceive('create')
            ->twice()
            ->andReturnValues(
                array(
                    new File(md5('some/file.php'), 'some/file.php'),
                    new File(md5('some/other.php'), 'some/other.php')
                )
            );

        $projectFactory = new ProjectFactory(array($someOtherStrategy, $fileStrategyMock));

        $files = array('some/file.php', 'some/other.php');
        $project = $projectFactory->create($files);

        $this->assertInstanceOf(Project::class, $project);

        $projectFilePaths = array_keys($project->getFiles());
        $this->assertEquals($files, $projectFilePaths);
    }

    /**
     * @covers ::create
     * @covers ::<private>
     *
     * @expectedException \OutOfBoundsException
     */
    public function testCreateThrowsExceptionWhenStrategyNotFound()
    {
        $projectFactory = new ProjectFactory(array());
        $projectFactory->create(array('aa'));
    }

    /**
     * @covers ::create
     * @covers ::<private>
     */
    public function testCreateProjectFromFileWithNamespacedClass()
    {
        $file = new File(md5('some/file.php'), 'some/file.php');
        $file->addNamespace(new Fqsen('\mySpace'));
        $file->addClass(new Class_(new Fqsen('\mySpace\MyClass')));

        $namespaces = $this->fetchNamespaces($file);

        $this->assertEquals('\mySpace', key($namespaces));

        /** @var Namespace_ $mySpace */
        $mySpace = current($namespaces);

        $this->assertInstanceOf(Namespace_::class, $mySpace);
        $this->assertEquals('\mySpace\MyClass', key($mySpace->getClasses()));
    }

    public function testWithNamespacedFunction()
    {
        $file = new File(md5('some/file.php'), 'some/file.php');
        $file->addNamespace(new Fqsen('\mySpace'));
        $file->addInterface(new Interface_(new Fqsen('\mySpace\MyInterface')));

        $namespaces = $this->fetchNamespaces($file);

        /** @var Namespace_ $mySpace */
        $mySpace = current($namespaces);

        $this->assertInstanceOf(Namespace_::class, $mySpace);
        $this->assertEquals('\mySpace\MyInterface', key($mySpace->getInterfaces()));
    }

    private function fetchNamespaces(File $file)
    {
        $fileStrategyMock = m::mock(ProjectFactoryStrategy::class);
        $fileStrategyMock->shouldReceive('matches')->once()->andReturn(true);
        $fileStrategyMock->shouldReceive('create')
            ->once()
            ->andReturnValues(
                array(
                    $file
                )
            );

        $projectFactory = new ProjectFactory(array($fileStrategyMock));
        $project = $projectFactory->create(array('some/file.php'));

        return $project->getNamespaces();
    }
}
