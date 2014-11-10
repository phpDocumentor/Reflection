<?php

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use Mockery as m;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Reflection\DocBlock\Tag\VarTag;
use phpDocumentor\Reflection\DocBlock\Type\Collection as TypeCollection;

class VarAssemblerTest extends \PHPUnit_Framework_TestCase
{
    /** @var VarAssembler */
    private $fixture;

    /** @var m\MockInterface|Analyzer */
    private $analyze;

    /**
     * Initializes the fixture for this test.
     */
    public function setUp()
    {
        $this->analyze = m::mock('phpDocumentor\Descriptor\Analyzer');
        $this->fixture = new VarAssembler();
        $this->fixture->setAnalyzer($this->analyze);
    }

    /**
     * @covers phpDocumentor\Descriptor\Builder\Reflector\Tags\VarAssembler::create
     */
    public function testCreatingVarDescriptorFromReflector()
    {
        $types = new Collection();
        $this->analyze->shouldReceive('analyze')
            ->with(
                m::on(
                    function ($value) {
                        return $value instanceof TypeCollection && $value[0] == 'string';
                    }
                )
            )
            ->andReturn($types);
        $reflector = new VarTag('var', 'string $myParameter This is a description');

        $descriptor = $this->fixture->create($reflector);

        $this->assertSame('var', $descriptor->getName());
        $this->assertSame('This is a description', $descriptor->getDescription());
        $this->assertSame('$myParameter', $descriptor->getVariableName());
        $this->assertSame($types, $descriptor->getTypes());
    }
}
