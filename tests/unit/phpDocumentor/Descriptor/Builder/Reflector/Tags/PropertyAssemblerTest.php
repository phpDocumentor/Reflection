<?php

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use Mockery as m;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Reflection\DocBlock\Tag\PropertyTag;
use phpDocumentor\Reflection\DocBlock\Type\Collection as TypeCollection;

class PropertyAssemblerTest extends \PHPUnit_Framework_TestCase
{
    /** @var PropertyAssembler */
    private $fixture;

    /** @var m\MockInterface|Analyzer */
    private $analyzer;

    /**
     * Initializes the fixture for this test.
     */
    public function setUp()
    {
        $this->analyzer = m::mock('phpDocumentor\Descriptor\Analyzer');
        $this->fixture = new PropertyAssembler();
        $this->fixture->setAnalyzer($this->analyzer);
    }

    /**
     * @covers phpDocumentor\Descriptor\Builder\Reflector\Tags\PropertyAssembler::create
     */
    public function testCreatingPropertyDescriptorFromReflector()
    {
        $types = new Collection();
        $this->analyzer->shouldReceive('analyze')
            ->with(
                m::on(
                    function ($value) {
                        return $value instanceof TypeCollection && $value[0] == 'string';
                    }
                )
            )
            ->andReturn($types);
        $reflector = new PropertyTag('property', 'string $myProperty This is a description');

        $descriptor = $this->fixture->create($reflector);

        $this->assertSame('property', $descriptor->getName());
        $this->assertSame('This is a description', $descriptor->getDescription());
        $this->assertSame('$myProperty', $descriptor->getVariableName());
        $this->assertSame($types, $descriptor->getTypes());
    }
}
