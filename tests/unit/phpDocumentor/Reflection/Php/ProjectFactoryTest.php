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

namespace phpDocumentor\Reflection\Php;

use Mockery as m;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Factory\DummyFactoryStrategy;
use PHPUnit\Framework\TestCase;

/**
 * Test case for ProjectFactory
 *
 * @coversDefaultClass phpDocumentor\Reflection\Php\ProjectFactory
 * @covers ::create
 * @covers ::<private>
 */
class ProjectFactoryTest extends TestCase
{

    protected function tearDown()
    {
        m::close();
    }

    /**
     * @covers ::__construct
     */
    public function testStrategiesAreChecked()
    {
        new ProjectFactory(array(new DummyFactoryStrategy()));
        $this->assertTrue(true);
    }

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
        $project = $projectFactory->create('MyProject', $files);

        $this->assertInstanceOf(Project::class, $project);

        $projectFilePaths = array_keys($project->getFiles());
        $this->assertEquals($files, $projectFilePaths);
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testCreateThrowsExceptionWhenStrategyNotFound()
    {
        $projectFactory = new ProjectFactory(array());
        $projectFactory->create('MyProject', array('aa'));
    }

    public function testCreateProjectFromFileWithNamespacedClass()
    {
        $file = new File(md5('some/file.php'), 'some/file.php');
        $file->addNamespace(new Fqsen('\mySpace'));
        $file->addClass(new Class_(new Fqsen('\mySpace\MyClass')));

        $namespaces = $this->fetchNamespacesFromSingleFile($file);

        $this->assertEquals('\mySpace', key($namespaces));

        /** @var Namespace_ $mySpace */
        $mySpace = current($namespaces);

        $this->assertInstanceOf(Namespace_::class, $mySpace);
        $this->assertEquals('\mySpace\MyClass', key($mySpace->getClasses()));
    }

    public function testWithNamespacedInterface()
    {
        $file = new File(md5('some/file.php'), 'some/file.php');
        $file->addNamespace(new Fqsen('\mySpace'));
        $file->addInterface(new Interface_(new Fqsen('\mySpace\MyInterface')));

        $namespaces = $this->fetchNamespacesFromSingleFile($file);

        /** @var Namespace_ $mySpace */
        $mySpace = current($namespaces);

        $this->assertInstanceOf(Namespace_::class, $mySpace);
        $this->assertEquals('\mySpace\MyInterface', key($mySpace->getInterfaces()));
    }

    public function testWithNamespacedFunction()
    {
        $file = new File(md5('some/file.php'), 'some/file.php');
        $file->addNamespace(new Fqsen('\mySpace'));
        $file->addFunction(new Function_(new Fqsen('\mySpace\function()')));

        $namespaces = $this->fetchNamespacesFromSingleFile($file);

        /** @var Namespace_ $mySpace */
        $mySpace = current($namespaces);

        $this->assertInstanceOf(Namespace_::class, $mySpace);
        $this->assertEquals('\mySpace\function()', key($mySpace->getFunctions()));
    }

    public function testWithNamespacedConstant()
    {
        $file = new File(md5('some/file.php'), 'some/file.php');
        $file->addNamespace(new Fqsen('\mySpace'));
        $file->addConstant(new Constant(new Fqsen('\mySpace::MY_CONST')));

        $namespaces = $this->fetchNamespacesFromSingleFile($file);

        /** @var Namespace_ $mySpace */
        $mySpace = current($namespaces);

        $this->assertInstanceOf(Namespace_::class, $mySpace);
        $this->assertEquals('\mySpace::MY_CONST', key($mySpace->getConstants()));
    }

    public function testWithNamespacedTrait()
    {
        $file = new File(md5('some/file.php'), 'some/file.php');
        $file->addNamespace(new Fqsen('\mySpace'));
        $file->addTrait(new Trait_(new Fqsen('\mySpace\MyTrait')));

        $namespaces = $this->fetchNamespacesFromSingleFile($file);

        /** @var Namespace_ $mySpace */
        $mySpace = current($namespaces);

        $this->assertInstanceOf(Namespace_::class, $mySpace);
        $this->assertEquals('\mySpace\MyTrait', key($mySpace->getTraits()));
    }

    public function testNamespaceSpreadOverMultipleFiles()
    {
        $someFile = new File(md5('some/file.php'), 'some/file.php');
        $someFile->addNamespace(new Fqsen('\mySpace'));
        $someFile->addClass(new Class_(new Fqsen('\mySpace\MyClass')));

        $otherFile = new File(md5('some/other.php'), 'some/other.php');
        $otherFile->addNamespace(new Fqsen('\mySpace'));
        $otherFile->addClass(new Class_(new Fqsen('\mySpace\OtherClass')));

        $namespaces = $this->fetchNamespacesFromMultipleFiles(array($otherFile, $someFile));

        $this->assertCount(1, $namespaces);
        $this->assertCount(2, current($namespaces)->getClasses());
    }

    public function testSingleFileMultipleNamespaces()
    {
        $someFile = new File(md5('some/file.php'), 'some/file.php');
        $someFile->addNamespace(new Fqsen('\mySpace'));
        $someFile->addClass(new Class_(new Fqsen('\mySpace\MyClass')));
        $someFile->addNamespace(new Fqsen('\mySpace\SubSpace'));
        $someFile->addClass(new Class_(new Fqsen('\mySpace\SubSpace\MyClass')));

        $namespaces = $this->fetchNamespacesFromSingleFile($someFile);

        $this->assertCount(2, $namespaces);
        $this->assertArrayHasKey('\mySpace', $namespaces);
        $this->assertArrayHasKey('\mySpace\SubSpace', $namespaces);

        $this->assertCount(1, $namespaces['\mySpace']->getClasses());
    }

    /**
     * Uses the ProjectFactory to create a Project and returns the namespaces created by the factory.
     *
     * @param File $file
     * @return Namespace_[] Namespaces of the project
     */
    private function fetchNamespacesFromSingleFile(File $file)
    {
        return $this->fetchNamespacesFromMultipleFiles(array($file));
    }

    /**
     * Uses the ProjectFactory to create a Project and returns the namespaces created by the factory.
     *
     * @param File[] $files
     * @return Namespace_[] Namespaces of the project
     */

    private function fetchNamespacesFromMultipleFiles($files)
    {
        $fileStrategyMock = m::mock(ProjectFactoryStrategy::class);
        $fileStrategyMock->shouldReceive('matches')->times(count($files))->andReturn(true);
        $fileStrategyMock->shouldReceive('create')
            ->times(count($files))
            ->andReturnValues(
                $files
            );

        $projectFactory = new ProjectFactory(array($fileStrategyMock));
        $project = $projectFactory->create('My Project', $files);

        return $project->getNamespaces();
    }
}
