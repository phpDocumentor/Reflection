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

use Mockery as m;
use phpDocumentor\Descriptor\File;
use phpDocumentor\Descriptor\Project;
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
}
