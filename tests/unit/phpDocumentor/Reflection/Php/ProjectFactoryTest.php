<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php;

use Mockery as m;
use phpDocumentor\Reflection\Exception;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Factory\DummyFactoryStrategy;
use PHPUnit\Framework\TestCase;
use function array_keys;
use function count;
use function current;
use function key;
use function md5;

/**
 * Test case for ProjectFactory
 *
 * @coversDefaultClass \phpDocumentor\Reflection\Php\ProjectFactory
 * @covers ::create
 * @covers ::<private>
 */
class ProjectFactoryTest extends TestCase
{
    protected function tearDown() : void
    {
        m::close();
    }

    /**
     * @covers ::__construct
     */
    public function testStrategiesAreChecked() : void
    {
        new ProjectFactory([new DummyFactoryStrategy()]);
        $this->assertTrue(true);
    }

    public function testCreate() : void
    {
        $someOtherStrategy = m::mock(ProjectFactoryStrategy::class);
        $someOtherStrategy->shouldReceive('matches')->twice()->andReturn(false);
        $someOtherStrategy->shouldReceive('create')->never();

        $fileStrategyMock = m::mock(ProjectFactoryStrategy::class);
        $fileStrategyMock->shouldReceive('matches')->twice()->andReturn(true);
        $fileStrategyMock->shouldReceive('create')
            ->twice()
            ->andReturnValues(
                [
                    new File(md5('some/file.php'), 'some/file.php'),
                    new File(md5('some/other.php'), 'some/other.php'),
                ]
            );

        $projectFactory = new ProjectFactory([$someOtherStrategy, $fileStrategyMock]);

        $files   = ['some/file.php', 'some/other.php'];
        $project = $projectFactory->create('MyProject', $files);

        $this->assertInstanceOf(Project::class, $project);

        $projectFilePaths = array_keys($project->getFiles());
        $this->assertEquals($files, $projectFilePaths);
    }

    public function testCreateThrowsExceptionWhenStrategyNotFound() : void
    {
        $this->expectException('OutOfBoundsException');
        $projectFactory = new ProjectFactory([]);
        $projectFactory->create('MyProject', ['aa']);
    }

    public function testCreateProjectFromFileWithNamespacedClass() : void
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

    public function testWithNamespacedInterface() : void
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

    public function testWithNamespacedFunction() : void
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

    public function testWithNamespacedConstant() : void
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

    public function testWithNamespacedTrait() : void
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

    public function testNamespaceSpreadOverMultipleFiles() : void
    {
        $someFile = new File(md5('some/file.php'), 'some/file.php');
        $someFile->addNamespace(new Fqsen('\mySpace'));
        $someFile->addClass(new Class_(new Fqsen('\mySpace\MyClass')));

        $otherFile = new File(md5('some/other.php'), 'some/other.php');
        $otherFile->addNamespace(new Fqsen('\mySpace'));
        $otherFile->addClass(new Class_(new Fqsen('\mySpace\OtherClass')));

        $namespaces = $this->fetchNamespacesFromMultipleFiles([$otherFile, $someFile]);

        $this->assertCount(1, $namespaces);
        $this->assertCount(2, current($namespaces)->getClasses());
    }

    public function testSingleFileMultipleNamespaces() : void
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

    public function testErrorScenarioWhenFileStrategyReturnsNull() : void
    {
        $fileStrategyMock = m::mock(ProjectFactoryStrategy::class);
        $fileStrategyMock->shouldReceive('matches')->twice()->andReturn(true);
        $fileStrategyMock->shouldReceive('create')
            ->twice()
            ->andReturnValues(
                [
                    null,
                    new File(md5('some/other.php'), 'some/other.php'),
                ]
            );

        $projectFactory = new ProjectFactory([$fileStrategyMock]);

        $files   = ['some/file.php', 'some/other.php'];
        $project = $projectFactory->create('MyProject', $files);

        $this->assertInstanceOf(Project::class, $project);

        $projectFilePaths = array_keys($project->getFiles());
        $this->assertEquals(['some/other.php'], $projectFilePaths);
    }

    /**
     * Uses the ProjectFactory to create a Project and returns the namespaces created by the factory.
     *
     * @return Namespace_[] Namespaces of the project
     *
     * @throws Exception
     */
    private function fetchNamespacesFromSingleFile(File $file) : array
    {
        return $this->fetchNamespacesFromMultipleFiles([$file]);
    }

    /**
     * Uses the ProjectFactory to create a Project and returns the namespaces created by the factory.
     *
     * @param File[] $files
     *
     * @return Namespace_[] Namespaces of the project
     *
     * @throws Exception
     */
    private function fetchNamespacesFromMultipleFiles(array $files) : array
    {
        $fileStrategyMock = m::mock(ProjectFactoryStrategy::class);
        $fileStrategyMock->shouldReceive('matches')->times(count($files))->andReturn(true);
        $fileStrategyMock->shouldReceive('create')
            ->times(count($files))
            ->andReturnValues(
                $files
            );

        $projectFactory = new ProjectFactory([$fileStrategyMock]);
        $project        = $projectFactory->create('My Project', $files);

        return $project->getNamespaces();
    }
}
