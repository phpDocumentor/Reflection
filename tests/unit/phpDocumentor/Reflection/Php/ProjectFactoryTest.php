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
     * @covers ::__construct
     *
     * @expectedException \InvalidArgumentException
     */
    public function testOnlyAcceptsStrategies()
    {
        new ProjectFactory(array(new \stdClass()));
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        $fileStrategyMock = m::mock(ProjectFactoryStrategy::class);
        $fileStrategyMock->shouldReceive('create')
            ->twice()
            ->andReturnValues(
                array(
                    new File(md5('some/file.php'), 'some/file.php'),
                    new File(md5('some/other.php'), 'some/other.php')
                )
            );

        $projectFactory = new ProjectFactory(array($fileStrategyMock));

        $files = array('some/file.php', 'some/other.php');
        $project = $projectFactory->create($files);

        $this->assertInstanceOf(Project::class, $project);

        $projectFilePaths = array_keys($project->getFiles());
        $this->assertEquals($files, $projectFilePaths);
    }
}
