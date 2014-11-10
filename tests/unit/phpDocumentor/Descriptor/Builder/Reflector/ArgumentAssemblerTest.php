<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @author    Sven Hagemann <sven@rednose.nl>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Descriptor\Builder\Reflector;

use Mockery as m;
use phpDocumentor\Reflection\DocBlock\Type\Collection;
use phpDocumentor\Descriptor\Analyzer;

/**
 * Test class for phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler
 */
class ArgumentAssemblerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ArgumentAssembler $fixture */
    protected $fixture;

    /** @var Analyzer|m\MockInterface */
    protected $analyzerMock;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp()
    {
        $this->analyzerMock = m::mock('phpDocumentor\Descriptor\Analyzer');
        $this->fixture = new ArgumentAssembler();
        $this->fixture->setAnalyzer($this->analyzerMock);
    }

    /**
     * @covers phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler::create
     */
    public function testCreateArgumentDescriptorFromReflector()
    {
        // Arrange
        $name = 'goodArgument';
        $type = 'boolean';

        $argumentReflectorMock = $this->givenAnArgumentReflectorWithNameAndType($name, $type);
        $types = $this->thenProjectBuilderShouldSetCollectionOfExpectedTypes(array($type));

        // Act
        $descriptor = $this->fixture->create($argumentReflectorMock);

        // Assert
        $this->assertSame($name, $descriptor->getName());
        $this->assertSame($types, $descriptor->getTypes());
        $this->assertSame(false, $descriptor->getDefault());
        $this->assertSame(false, $descriptor->isByReference());
    }

    /**
     * @covers phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler::create
     * @covers phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler::overwriteTypeAndDescriptionFromParamTag
     */
    public function testIfTypeAndDescriptionAreSetFromParamDescriptor()
    {
        // Arrange
        $name = 'goodArgument';
        $type = 'boolean';

        $argumentReflectorMock = $this->givenAnArgumentReflectorWithNameAndType($name, $type);
        $types = $this->thenProjectBuilderShouldSetCollectionOfExpectedTypes(array($type));

        // Mock a paramDescriptor
        $paramDescriptorTagMock = m::mock('phpDocumentor\Descriptor\Tag\ParamDescriptor');
        $paramDescriptorTagMock->shouldReceive('getVariableName')->once()->andReturn($name);
        $paramDescriptorTagMock->shouldReceive('getDescription')->once()->andReturn('Is this a good argument, or nah?');
        $paramDescriptorTagMock->shouldReceive('getTypes')->once()->andReturn($types);

        // Act
        $descriptor = $this->fixture->create($argumentReflectorMock, array($paramDescriptorTagMock));

        // Assert
        $this->assertSame($name, $descriptor->getName());
        $this->assertSame($types, $descriptor->getTypes());
        $this->assertSame(false, $descriptor->getDefault());
        $this->assertSame(false, $descriptor->isByReference());
    }

    /**
     * @param $name
     * @param $type
     * @return m\MockInterface
     */
    protected function givenAnArgumentReflectorWithNameAndType($name, $type)
    {
        $argumentReflectorMock = m::mock('phpDocumentor\Reflection\FunctionReflector\ArgumentReflector');
        $argumentReflectorMock->shouldReceive('getName')->andReturn($name);
        $argumentReflectorMock->shouldReceive('getType')->andReturn($type);
        $argumentReflectorMock->shouldReceive('getDefault')->andReturn(false);
        $argumentReflectorMock->shouldReceive('isByRef')->andReturn(false);

        return $argumentReflectorMock;
    }

    /**
     * @param $expected
     * @return Collection
     */
    protected function thenProjectBuilderShouldSetCollectionOfExpectedTypes($expected)
    {
        $types = new Collection($expected);
        $this->analyzerMock->shouldReceive('analyze')
            ->with(
                m::on(
                    function ($value) use ($expected) {
                        return $value instanceof Collection && $value->getArrayCopy() == $expected;
                    }
                )
            )
            ->andReturn($types);
        return $types;
    }
}
