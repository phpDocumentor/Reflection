<?php

namespace phpDocumentor\Descriptor;

use Mockery as m;
use phpDocumentor\Descriptor\Interfaces\ProjectInterface;
use phpDocumentor\Reflection\Fqsen;

/**
 * Tests for the \phpDocumentor\Descriptor\ProjectAnalyzer class.
 */
class ProjectAnalyzerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ProjectAnalyzer */
    private $fixture;

    protected function setUp()
    {
        $this->fixture = new ProjectAnalyzer();
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectAnalyzer::analyze
     */
    public function testFilesAreCounted()
    {
        // Arrange
        $projectDescriptor = $this->givenAProjectMock();
        $this->whenProjectDescriptorHasTheFollowingFiles($projectDescriptor, array(1,2,3,4));
        $this->whenProjectDescriptorHasTheFollowingElements($projectDescriptor, array());
        $this->whenProjectHasTheFollowingChildrenOfRootNamespace($projectDescriptor, array());

        // Act
        $this->fixture->analyze($projectDescriptor);

        // Assert
        $this->assertAttributeSame(4, 'fileCount', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectAnalyzer::analyze
     */
    public function testIfTopLevelNamespacesAreCounted()
    {
        // Arrange
        $projectDescriptor = $this->givenAProjectMock();
        $this->whenProjectDescriptorHasTheFollowingFiles($projectDescriptor, array());
        $this->whenProjectDescriptorHasTheFollowingElements($projectDescriptor, array());
        $this->whenProjectHasTheFollowingChildrenOfRootNamespace($projectDescriptor, array(1,2,3));

        // Act
        $this->fixture->analyze($projectDescriptor);

        // Assert
        $this->assertAttributeSame(3, 'topLevelNamespaceCount', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectAnalyzer::analyze
     * @covers phpDocumentor\Descriptor\ProjectAnalyzer::findAllElements
     * @covers phpDocumentor\Descriptor\ProjectAnalyzer::incrementUnresolvedParentCounter
     */
    public function testIfUnresolvedClassesAreCounted()
    {
        // Arrange
        $classDescriptor1  = $this->givenAClassWithParent('phpDocumentor\Descriptor\ClassDescriptor');
        $projectDescriptor = $this->givenAProjectMock();
        $this->whenProjectDescriptorHasTheFollowingFiles($projectDescriptor, array());
        $this->whenProjectDescriptorHasTheFollowingElements(
            $projectDescriptor,
            array(
                'ds1' => $classDescriptor1,
                'ds2' => $this->givenAClassWithParent($classDescriptor1),
                'ds3' => $this->givenAnInterfaceWithParent('123')
            )
        );
        $this->whenProjectHasTheFollowingChildrenOfRootNamespace($projectDescriptor, array());

        // Act
        $this->fixture->analyze($projectDescriptor);

        // Assert
        $this->assertAttributeSame(0, 'unresolvedParentClassesCount', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectAnalyzer::analyze
     * @covers phpDocumentor\Descriptor\ProjectAnalyzer::findAllElements
     * @covers phpDocumentor\Descriptor\ProjectAnalyzer::addElementToCounter
     */
    public function testIfVariousDescriptorTypesAreCounted()
    {
        // Arrange
        $classDescriptor1  = $this->givenAClassWithParent('phpDocumentor\Descriptor\ClassDescriptor');
        $projectDescriptor = $this->givenAProjectMock();
        $this->whenProjectDescriptorHasTheFollowingFiles($projectDescriptor, array());
        $this->whenProjectDescriptorHasTheFollowingElements(
            $projectDescriptor,
            array(
                'ds1' => $classDescriptor1,
                'ds2' => $this->givenAClassWithParent($classDescriptor1),
                'ds3' => $this->givenAnInterfaceWithParent('123')
            )
        );
        $this->whenProjectHasTheFollowingChildrenOfRootNamespace($projectDescriptor, array());

        // Act
        $this->fixture->analyze($projectDescriptor);

        // Assert
        $this->assertAttributeSame(
            array(
                'phpDocumentor\Descriptor\Class_'     => 2,
                'phpDocumentor\Descriptor\Interface_' => 1,
            ),
            'descriptorCountByType',
            $this->fixture
        );
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectAnalyzer::__toString
     */
    public function testIfStringOutputContainsAllCounters()
    {
        // Arrange
        $classDescriptor1  = $this->givenAClassWithParent('phpDocumentor\Descriptor\ClassDescriptor');
        $projectDescriptor = $this->givenAProjectMock();
        $this->whenProjectDescriptorHasTheFollowingFiles($projectDescriptor, array(1,2,3,4));
        $this->whenProjectDescriptorHasTheFollowingElements(
            $projectDescriptor,
            array(
                'ds1' => $classDescriptor1,
                'ds2' => $this->givenAClassWithParent($classDescriptor1),
                'ds3' => $this->givenAnInterfaceWithParent('123')
            )
        );
        $this->whenProjectHasTheFollowingChildrenOfRootNamespace($projectDescriptor, array(1,2,3));
        $this->fixture->analyze($projectDescriptor);

        $expected = <<<TEXT
In the Project are:
         4 files
         3 top-level namespaces
         0 unresolvable parent classes
         2 phpDocumentor\Descriptor\Class_ elements
         1 phpDocumentor\Descriptor\Interface_ elements

TEXT;

        // Act
        $result = (string) $this->fixture;

        // Assert
        $this->assertSame($expected, $result);
    }

    /**
     * Returns a class with the given parent set.
     *
     * @param string|DescriptorAbstract $parent
     *
     * @return Class_
     */
    protected function givenAClassWithParent($parent)
    {
        $classDescriptor1 = new Class_(new Fqsen('\My\Class'));
        return $classDescriptor1;
    }

    /**
     * @param $interfaceParent
     * @return Interface_
     */
    protected function givenAnInterfaceWithParent($interfaceParent)
    {
        $classDescriptor3 = new Interface_(new Fqsen('\My\Interface'));

        return $classDescriptor3;
    }

    /**
     * Returns a mocked Project object.
     *
     * @return m\Mock|ProjectInterface
     */
    protected function givenAProjectMock()
    {
        return m::mock('phpDocumentor\Descriptor\Interfaces\ProjectInterface')->shouldIgnoreMissing();
    }

    /**
     * Ensures that the ProjectInterface contains and returns the provided files.
     *
     * @param m\Mock|ProjectInterface $projectDescriptor
     * @param array                   $files
     *
     * @return void
     */
    protected function whenProjectDescriptorHasTheFollowingFiles($projectDescriptor, array $files)
    {
        $projectDescriptor->shouldReceive('getFiles')->andReturn($files);
    }

    /**
     * Ensures that the ProjectInterface has an index 'elements' with the provided elements.
     *
     * @param m\Mock|Project $projectDescriptor
     * @param array                    $elements
     *
     * @return void
     */
    protected function whenProjectDescriptorHasTheFollowingElements($projectDescriptor, array $elements)
    {
        $projectDescriptor->shouldReceive('getIndexes->get')
            ->with('elements', m::type('phpDocumentor\Descriptor\Collection'))->andReturn(new Collection($elements));
    }

    /**
     * Ensures that the Project has a root namespace with the provided array as children of that namespace.
     *
     * @param m\Mock|ProjectInterface $projectDescriptor
     * @param array $rootNamespaceChildren
     *
     * @return void
     */
    protected function whenProjectHasTheFollowingChildrenOfRootNamespace($projectDescriptor, $rootNamespaceChildren)
    {
        $projectDescriptor->shouldReceive('getNamespace->getChildren')->andReturn(
            new Collection($rootNamespaceChildren)
        );
    }
}
